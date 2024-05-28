<?php

namespace App\Repositories;

use App\Models\WaterIntake;
use App\Models\WaterIntakeContainer;

class WaterIntakeRepository
{
    public function create(array $data): WaterIntake
    {
        return WaterIntake::create($data);
    }

    public function find(int $id): ?WaterIntake
    {
        return WaterIntake::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $waterIntake = $this->find($id);

        if ($waterIntake) {
            return $waterIntake->update($data);
        }

        return false;
    }

    //função para deletar um registro de consumo de água, deve retornar o registro deletado ou false em caso de erro
    public function delete(int $id): ?WaterIntake
    {
        $waterIntake = $this->find($id);

        if ($waterIntake) {
            $waterIntake->delete();
            return $waterIntake;
        }

        return null;
    }


    public function getWaterIntakesByDay(int $userId, string $date)
    {
        return WaterIntake::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();
    }

    public function getTotalWaterIntakeByDay(int $userId, string $date)
    {
        return WaterIntake::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->sum('amount');
    }

    public function getWaterIntakesByMonth(int $userId, string $date)
    {
        return WaterIntake::where('user_id', $userId)
            ->whereMonth('created_at', $date)
            ->get();
    }

    public function getWaterIntakesByYear(int $userId, string $date)
    {
        return WaterIntake::where('user_id', $userId)
            ->whereYear('created_at', $date)
            ->get();
    }

    public function getWaterIntakesByDateRange(int $userId, string $startDate, string $endDate)
    {
        return WaterIntake::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function getWaterIntakesByDateRangeAndContainer(int $userId, string $startDate, string $endDate, int $containerId)
    {
        return WaterIntake::where('user_id', $userId)
            ->where('water_intake_container_id', $containerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    public function getWaterIntakesByContainer(int $userId, int $containerId)
    {
        return WaterIntake::where('user_id', $userId)
            ->where('water_intake_container_id', $containerId)
            ->get();
    }

    public function createWaterIntakeContainer(array $data)
    {
        return WaterIntakeContainer::create($data);
    }

    public function findWaterIntakeContainer(int $id): ?WaterIntakeContainer
    {
        return WaterIntakeContainer::find($id);
    }

    public function updateWaterIntakeContainer(int $id, array $data): bool
    {
        $waterIntakeContainer = $this->findWaterIntakeContainer($id);

        if ($waterIntakeContainer) {
            return $waterIntakeContainer->update($data);
        }

        return false;
    }

    public function findWaterIntakeContainersByUser(int $userId)
    {
        return WaterIntakeContainer::where('user_id', $userId)->get();
    }
}
