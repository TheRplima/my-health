<?php

namespace App\Repositories;

use App\Models\WeightControl;

class WeightControlRepository
{
    public function create(array $data): WeightControl
    {
        return WeightControl::create($data);
    }

    public function find(int $id): ?WeightControl
    {
        return WeightControl::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $WeightControl = $this->find($id);

        if ($WeightControl) {
            return $WeightControl->update($data);
        }

        return false;
    }

    public function delete(int $id): ?WeightControl
    {
        $WeightControl = $this->find($id);

        if ($WeightControl) {
            $WeightControl->delete();
            return $WeightControl;
        }

        return null;
    }

    public function getWeightControlsByUser(int $userId, $max = null)
    {
        $query = WeightControl::where('user_id', $userId);

        if ($max) {
            $query->take($max);
        }

        return $query->get();
    }

    public function getWeightControlsByDay(int $userId, string $date)
    {
        return WeightControl::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();
    }

    public function getWeightControlsByMonth(int $userId, string $date)
    {
        return WeightControl::where('user_id', $userId)
            ->whereMonth('created_at', $date)
            ->get();
    }

    public function getWeightControlsByYear(int $userId, string $date)
    {
        return WeightControl::where('user_id', $userId)
            ->whereYear('created_at', $date)
            ->get();
    }

    public function getWeightControlsByDateRange(int $userId, string $startDate, string $endDate, $max = null)
    {
        $query = WeightControl::where('user_id', $userId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($max) {
            $query->take($max);
        }

        return $query->get();
    }

    public function getLatestWeightControl(int $userId)
    {
        return WeightControl::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
