<?php

namespace App\Services;

use App\Exceptions\FailedAction;
use App\Hooks\SendCallbackQueryHomeAssistant;
use App\Http\Resources\WaterIntakeCollection;
use App\Repositories\WaterIntakeRepository;
use App\Http\Resources\WaterIntakeResource;
use Illuminate\Http\Response;

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
}
