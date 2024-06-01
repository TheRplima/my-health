<?php

namespace App\Services;

use App\Repositories\WeightControlRepository;
use App\Exceptions\FailedAction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;

class WeightControlService
{
    private $weightControlRepository;

    public function __construct(WeightControlRepository $weightControlRepository)
    {
        $this->weightControlRepository = $weightControlRepository;
    }

    public function create(array $data)
    {
        try {
            if (isset($data['date']) && $data['date']) {
                $date = Carbon::createFromFormat('Y-m-d', $data['date']);
                $data['created_at'] = $date;
                $data['updated_at'] = $date;
                unset($data['date']);
            }
            $weightControl = $this->weightControlRepository->create($data);

            $user = User::find($data['user_id']);
            $user->weight = $weightControl->weight;
            $user->save();

            return $weightControl;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao registrar o peso. Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $weightControl = $this->weightControlRepository->update($id, $data);

            return $weightControl;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao atualizar o peso', Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $weightControl = $this->weightControlRepository->delete($id);

            if ($weightControl) {

                $user = Auth::user();
                $latestWeight = $this->weightControlRepository->getLatestWeightControl($user->id);
                if ($latestWeight != null) {
                    $user->weight = $latestWeight->weight;
                    $user->save();
                }

                return $weightControl;
            }

            return false;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao excluir o peso. Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getById(int $id)
    {
        // Get a specific weight control record by ID
        return $this->weightControlRepository->find($id);
    }

    public function getWeightControlsByUser(int $userId, $max = null)
    {
        // Get all weight control records
        return $this->weightControlRepository->getWeightControlsByUser($userId, $max);
    }

    public function getWeightControlsByDay(int $userId, string $date)
    {
        // Get weight control records by day
        return $this->weightControlRepository->getWeightControlsByDay($userId, $date);
    }

    public function getWeightControlsByMonth(int $userId, string $date)
    {
        // Get weight control records by month
        return $this->weightControlRepository->getWeightControlsByMonth($userId, $date);
    }

    public function getWeightControlsByYear(int $userId, string $date)
    {
        // Get weight control records by year
        return $this->weightControlRepository->getWeightControlsByYear($userId, $date);
    }

    public function getWeightControlsByDateRange(int $userId, string $startDate, string $endDate,  $max = null)
    {
        // Get weight control records by date range
        return $this->weightControlRepository->getWeightControlsByDateRange($userId, $startDate, $endDate,  $max);
    }

    public function getThisYearBodyWeightVariationChartData(int $userId)
    {
        $start = now()->startOfYear();
        $end = now()->endOfYear();
        $weightControls = $this->getWeightControlsByDateRange($userId, $start->toDateString(), $end->toDateString());

        for ($i = $start; $i <= $end; $i->addDay()) {
            $data[] = [
                'date' => $i,
                'weight' => 0,
            ];
        }

        $data = [];
        foreach ($weightControls as $weightControl) {
            $data[] = [
                $weightControl->created_at->format('d/m'),
                $weightControl->weight,
            ];
        }

        $data = array_merge([['Data', 'Peso']], $data);

        return $data;
    }

    public function showWeightForThisMonth(User $user)
    {
        $object = $this->getWeightControlsByMonth($user->id, now()->month);

        if ($object) {
            $message = "Seu peso atual é de *{$user->weight}kg*.\n\n";
            $message .= "Detalhes:\n";
            $message .= "Peso registrado este mês:\n";
            foreach ($object as $item) {
                $message .= $item->created_at->format('d/m/Y') . ' - ';
                $message .= str_replace('.', ',', (string) $item->weight) . "kg\n";
            }

            return $message;
        }

        return 'Você não registrou seu peso este mês.';
    }

    public function showWeightForThisYear(User $user)
    {
        $object = $this->getWeightControlsByYear($user->id, now()->year);

        if ($object) {
            $message = "Seu peso atual é de *{$user->weight}kg*.\n\n";
            $message .= "Detalhes:\n";
            $message .= "Peso registrado este ano:\n";
            foreach ($object as $item) {
                $message .= $item->created_at->format('d/m/Y') . ' - ';
                $message .= str_replace('.', ',', (string) $item->weight) . "kg\n";
            }

            return $message;
        }

        return 'Você não registrou seu peso este mês.';
    }
}
