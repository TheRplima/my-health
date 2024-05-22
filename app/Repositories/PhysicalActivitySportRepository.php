<?php

namespace App\Repositories;

use App\Models\PhysicalActivitySport;

class PhysicalActivitySportRepository
{
    public function getAll(array $filters)
    {
        $query = PhysicalActivitySport::query();

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->get();
    }

    public function create(array $data): PhysicalActivitySport
    {
        return PhysicalActivitySport::create($data);
    }

    public function find(int $id): ?PhysicalActivitySport
    {
        return PhysicalActivitySport::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $physicalActivitySport = $this->find($id);

        if ($physicalActivitySport) {
            return $physicalActivitySport->update($data);
        }

        return false;
    }

    public function delete(int $id): ?PhysicalActivitySport
    {
        $physicalActivitySport = $this->find($id);

        if ($physicalActivitySport) {
            $physicalActivitySport->delete();
            return $physicalActivitySport;
        }

        return null;
    }
}
