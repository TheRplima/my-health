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

            if ($data['user_id'] == 1) {
                $sendCallbackQueryHomeAssistant = new SendCallbackQueryHomeAssistant();
                $sendCallbackQueryHomeAssistant(['amount' => $data['amount']]);
            }

            return new WaterIntakeResource($waterIntake);
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
                if ($waterIntake->user_id == 1) {
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
        // Get water intake records by day
        return new WaterIntakeCollection(
            $this->waterIntakeRepository->getWaterIntakesByDay($userId, $date)
        );
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
                    'callback_data' => 'WaterIntake_create_amount:' . $item->size
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
                    'callback_data' => 'WaterIntake_create_amount:cancel'
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
