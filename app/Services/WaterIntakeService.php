<?php

namespace App\Services;

use App\Hooks\SendCallbackQueryHomeAssistant;
use App\Http\Resources\WaterIntakeCollection;
use App\Repositories\WaterIntakeRepository;
use App\Http\Resources\WaterIntakeResource;
use App\Exceptions\FailedAction;
use Illuminate\Http\Response;
use App\Models\User;

class WaterIntakeService
{
    private $waterIntakeRepository;

    public function __construct(WaterIntakeRepository $waterIntakeRepository)
    {
        $this->waterIntakeRepository = $waterIntakeRepository;
    }

    public function create(array $data)
    {
        try {
            $waterIntake = $this->waterIntakeRepository->create($data);

            if ($data['user_id'] == 1 && config('app.env') == 'production') {
                $sendCallbackQueryHomeAssistant = new SendCallbackQueryHomeAssistant();
                $sendCallbackQueryHomeAssistant(['amount' => $data['amount']]);
            }

            if ($waterIntake) {
                $user = User::find($data['user_id']);
                $total = $this->waterIntakeRepository->getTotalWaterIntakeByDay($data['user_id'], now()->toDateString());
                if ($user->daily_water_amount) {
                    $msg = $total >= $user->daily_water_amount ? 'Parabéns! Você já atingiu sua meta de consumo diário de água.' : 'Faltam ' . ($user->daily_water_amount - $total) . 'ml para atingir sua meta de consumo diário de água.';
                } else {
                    $msg = 'Você ainda não definiu sua meta de consumo diário de água. Para definir sua meta de consumo diário de água acesse o menu Perfil de Usuário.';
                }
                return [
                    'success' => 'Consumo de água registrado com sucesso. Você já consumiu ' . $total . 'ml de água hoje. ' . $msg,
                    'data' => $waterIntake
                ];
            } else {
                return [
                    'error' => 'Erro ao registrar o consumo de água.'
                ];
            }
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao registrar o consumo de água. Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $waterIntake = $this->waterIntakeRepository->update($id, $data);
            return $waterIntake;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao atualizar o consumo de água', Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $waterIntake = $this->waterIntakeRepository->delete($id);

            if ($waterIntake) {
                if ($waterIntake->user_id == 1 && config('app.env') == 'production') {
                    $sendCallbackQueryHomeAssistant = new SendCallbackQueryHomeAssistant();
                    $sendCallbackQueryHomeAssistant(['amount' => ($waterIntake->amount * -1)]);
                }

                return new WaterIntakeResource($waterIntake);
            }

            return false;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao excluir o consumo de água Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getById(int $id)
    {
        // Get a specific water intake record by ID
        return new WaterIntakeResource($this->waterIntakeRepository->find($id));
    }

    public function getWaterIntakesByDay(int $userId, string $date)
    {
        // Get water intake records by day and return as a collection and  the total amount for the day
        $waterIntakes = $this->waterIntakeRepository->getWaterIntakesByDay($userId, $date);
        $totalAmount = $this->waterIntakeRepository->getTotalWaterIntakeByDay($userId, $date);

        return [
            'water_intakes' => new WaterIntakeCollection($waterIntakes),
            'total_water_intake' => $totalAmount
        ];
    }

    public function getTotalWaterIntakeByDay(int $userId, string $date)
    {
        // Get total water intake by day
        return $this->waterIntakeRepository->getTotalWaterIntakeByDay($userId, $date);
    }

    public function getWaterIntakesByMonth(int $userId, string $date)
    {
        // Get water intake records by month
        return new WaterIntakeCollection(
            $this->waterIntakeRepository->getWaterIntakesByMonth($userId, $date)
        );
    }

    public function getWaterIntakesByYear(int $userId, string $date)
    {
        // Get water intake records by year
        return new WaterIntakeCollection(
            $this->waterIntakeRepository->getWaterIntakesByYear($userId, $date)
        );
    }

    public function getWaterIntakesByDateRange(int $userId, string $startDate, string $endDate)
    {
        // Get water intake records by date range
        return new WaterIntakeCollection(
            $this->waterIntakeRepository->getWaterIntakesByDateRange($userId, $startDate, $endDate)
        );
    }

    public function getThisWeekWaterIntakeChartData(int $userId, $daily_goal = null)
    {
        $data = [];
        $start = now()->startOfWeek(0);
        $end = now()->endOfWeek(6);
        $total = 0;
        for ($i = $start; $i <= $end; $i->addDay()) {
            $amount = $this->getTotalWaterIntakeByDay($userId, $i->toDateString());
            $total += $amount;
            $data[] = [$i->format('d/m'), round($amount), $daily_goal];
        }
        $data = array_merge([["Dia", "Consumo de água", "Meta"]], $data);

        return $data;
    }

    public function getThisMonthWaterIntakeChartData(int $userId, $daily_goal = null)
    {
        $data = [];
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $waterIntakes = $this->getWaterIntakesByDateRange($userId, $start->toDateString(), $end->toDateString());
        $weeks = [];
        for ($i = $start; $i <= $end; $i->addDay()) {
            $weekStart = $i->copy()->startOfWeek(0);
            $weekEnd = $i->copy()->endOfWeek(6);
            if ($weekStart->month != $i->month) {
                $weekStart = $i->copy()->startOfMonth();
            }
            if ($weekEnd->month != $i->month) {
                $weekEnd = $i->copy()->endOfMonth();
            }
            $week = $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m');
            if (!isset($weeks[$week])) {
                $weeks[$week] = [0, 0];
            }
            $date = $i->toDateString();
            //map the water intake amount to the week
            $waterIntakesOfDay = $waterIntakes->filter(function ($waterIntake) use ($date) {
                return $waterIntake->created_at->toDateString() == $date;
            });
            $amount = $waterIntakesOfDay->sum('amount');
            $weekAmount = $weeks[$week][0] + $amount;
            $weekGoal = $weeks[$week][1] + $daily_goal;
            $weeks[$week] = [$weekAmount, $weekGoal];
        }
        foreach ($weeks as $week => $values) {
            $data[] = [$week, (int)$values[0], $values[1]];
        }

        $data = array_merge([["Semana", "Consumo de água", "Meta"]], $data);

        return $data;
    }

    public function showWaterIntakeToday(User $user)
    {

        $waterintakes = $this->getWaterIntakesByDay($user->id, now()->toDateString());
        if ($waterintakes) {
            $total = $waterintakes->sum('amount');
            $list = '';
            foreach ($waterintakes as $waterintake) {
                $list .= $waterintake->created_at->format('H:i') . ' - ';
                $list .= $waterintake->amount . 'ml' . "\n";
            }

            if (!$user->daily_water_amount) {
                $message = "Você ainda não definiu sua meta de consumo diário de água. Para definir sua meta de consumo diário de água acesse o menu Perfil de Usuário.\n";
            } else {
                $message = "Sua meta de consumo diário é de *{$user->daily_water_amount} ml*\n";
            }

            $message .= "Você já consumiu *{$total}ml* de água hoje.\n";

            if ($total >= $user->daily_water_amount) {
                $message .= "Parabéns! Você já atingiu sua meta de consumo diário de água.\n";
            } else {
                $missing = $user->daily_water_amount - $total;
                $message .= "Faltam *{$missing}ml* para atingir sua meta de consumo diário de água.\n";
            }
            if ($list) {
                $message .= "\nDetalhes:\n" . $list;
            }

            return $message;
        }

        return 'Você ainda não consumiu água hoje.';
    }

    public function createWaterIntakeContainer(array $data)
    {
        try {
            $waterIntakeContainer = $this->waterIntakeRepository->createWaterIntakeContainer($data);
            return $waterIntakeContainer;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao criar recipiente de água. Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getWaterIntakeContainersByUser(int $userId)
    {
        // Get all water intake containers by user
        return $this->waterIntakeRepository->findWaterIntakeContainersByUser($userId);
    }

    public function showWaterIntakeContainers(User $user)
    {
        $object = $this->getWaterIntakeContainersByUser($user->id);

        if ($object) {
            $list = '';
            foreach ($object as $item) {
                $list .= $item->name . ' - ';
                $list .= $item->size . 'ml' . "\n";
            }

            $message = 'Detalhes:' . "\n" . $list;

            return $message;
        }

        return 'Você ainda não possui recipientes de água cadastrados.';
    }

    public function showAmountOptions($user_id)
    {
        $user = User::find($user_id);
        $object = $this->getWaterIntakeContainersByUser($user->id);

        if ($object && count($object->toArray(request())) > 0) {
            $return = [];
            $options = [];
            $i = 0;
            foreach ($object as $item) {
                $options[] = [
                    'text' => $item->name,
                    'callback_data' => 'Bot_WaterIntake_create|amount:' . $item->size
                ];
                $i++;
                if ($i == 3) {
                    $return[] = $options;
                    $options = [];
                    $i = 0;
                }
            }
            if ($i > 0) {
                $return[] = $options;
            }
            $return[] = [
                [
                    'text' => 'Cancelar',
                    'callback_data' => 'Bot_WaterIntake_create|cancel'
                ]
            ];

            return [
                'text' => 'Selecione a quantidade de água consumida:',
                'inline_keyboard' => $return
            ];
        }

        return [
            'error' => 'Você ainda não possui recipientes de água cadastrados.'
        ];
    }
}
