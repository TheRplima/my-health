<?php

namespace App\Repositories;

use App\Models\WaterIntake;

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

    public function delete(int $id): bool
    {
        $waterIntake = $this->find($id);

        if ($waterIntake) {
            return $waterIntake->delete();
        }

        return false;
    }

    public function getWaterIntakesByDay(int $userId, string $date)
    {
        return WaterIntake::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();
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
}
