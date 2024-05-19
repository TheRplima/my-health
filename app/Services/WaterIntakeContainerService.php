<?php

namespace App\Services;

use App\Repositories\WaterIntakeContainerRepository;
use App\Http\Resources\WaterIntakeContainerResource;
use App\Exceptions\FailedAction;
use App\Http\Resources\WaterIntakeContainerCollection;
use App\Models\User;
use Illuminate\Http\Response;

class WaterIntakeContainerService
{
    private $waterIntakeContainerRepository;

    public function __construct(WaterIntakeContainerRepository $waterIntakeContainerRepository)
    {
        $this->waterIntakeContainerRepository = $waterIntakeContainerRepository;
    }

    public function create(array $data)
    {
        try {
            $waterIntakeContainer = $this->waterIntakeContainerRepository->create($data);

            return new WaterIntakeContainerResource($waterIntakeContainer);
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao registrar recipiente de água. Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $waterIntakeContainer = $this->waterIntakeContainerRepository->update($id, $data);
            return $waterIntakeContainer;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao atualizar o recipiente de água', Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $waterIntakeContainer = $this->waterIntakeContainerRepository->delete($id);

            if ($waterIntakeContainer) {

                return new WaterIntakeContainerResource($waterIntakeContainer);
            }

            return false;
        } catch (\Exception $e) {
            throw new FailedAction('Falha ao excluir o recipiente de água. Erro: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getById(int $id)
    {
        // Get a specific water intake record by ID
        return new WaterIntakeContainerResource($this->waterIntakeContainerRepository->find($id));
    }

    public function getWaterIntakeContainersByUser(int $userId)
    {
        // Get all water intake containers by user
        return $this->waterIntakeContainerRepository->findByUser($userId);
    }

    public function showWaterIntakeContainers(User $user)
    {
        $object = $this->getWaterIntakeContainersByUser($user->id);

        if ($object) {
            $list = '';
            foreach ($object as $item) {
                $list .= $item->name . ' \- ';
                $list .= $item->size . 'ml' . "\n";
            }

            $message = 'Detalhes:' . "\n" . $list;

            return $message;
        }

        return 'Você ainda não possui recipientes de água cadastrados\.';
    }
}
