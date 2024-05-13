<?php

namespace App\Repositories;

use App\Models\WaterIntakeContainer;

class WaterIntakeContainerRepository
{
    public function create(array $data): WaterIntakeContainer
    {
        return WaterIntakeContainer::create($data);
    }

    public function find(int $id): ?WaterIntakeContainer
    {
        return WaterIntakeContainer::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $waterIntakeContainer = $this->find($id);

        if ($waterIntakeContainer) {
            return $waterIntakeContainer->update($data);
        }

        return false;
    }

    //função para deletar um registro de consumo de água, deve retornar o registro deletado ou false em caso de erro
    public function delete(int $id): ?WaterIntakeContainer
    {
        $waterIntakeContainer = $this->find($id);

        if ($waterIntakeContainer) {
            $waterIntakeContainer->delete();
            return $waterIntakeContainer;
        }

        return null;
    }

    public function findByUser(int $userId): array
    {
        return WaterIntakeContainer::where('user_id', $userId)->get()->toArray();
    }
}
