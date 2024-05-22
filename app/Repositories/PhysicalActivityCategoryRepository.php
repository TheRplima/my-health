<?php

namespace App\Repositories;

use App\Models\PhysicalActivityCategory;

class PhysicalActivityCategoryRepository
{

    public function getAll()
    {
        return PhysicalActivityCategory::all();
    }

    public function create(array $data): PhysicalActivityCategory
    {
        return PhysicalActivityCategory::create($data);
    }

    public function find(int $id): ?PhysicalActivityCategory
    {
        return PhysicalActivityCategory::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $physicalActivityCategory = $this->find($id);

        if ($physicalActivityCategory) {
            return $physicalActivityCategory->update($data);
        }

        return false;
    }

    public function delete(int $id): ?PhysicalActivityCategory
    {
        $physicalActivityCategory = $this->find($id);

        if ($physicalActivityCategory) {
            $physicalActivityCategory->delete();
            return $physicalActivityCategory;
        }

        return null;
    }
}
