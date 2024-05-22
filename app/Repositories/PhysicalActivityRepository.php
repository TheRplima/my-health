<?php

namespace App\Repositories;

use App\Models\PhysicalActivity;

class PhysicalActivityRepository
{

    //get all physical activities from user, allowing filter by date range, category and sport
    public function getAll(int $userId, array $filters)
    {
        $query = PhysicalActivity::where('user_id', $userId);

        if (isset($filters['start_date'])) {
            $query->where('date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('date', '<=', $filters['end_date']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['sport_id'])) {
            $query->where('sport_id', $filters['sport_id']);
        }

        return $query->get();
    }

    public function create(array $data): PhysicalActivity
    {
        return PhysicalActivity::create($data);
    }

    public function find(int $id): ?PhysicalActivity
    {
        return PhysicalActivity::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $physicalActivity = $this->find($id);

        if ($physicalActivity) {
            return $physicalActivity->update($data);
        }

        return false;
    }

    public function delete(int $id): ?PhysicalActivity
    {
        $physicalActivity = $this->find($id);

        if ($physicalActivity) {
            $physicalActivity->delete();
            return $physicalActivity;
        }

        return null;
    }
}
