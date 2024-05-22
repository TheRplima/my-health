<?php

namespace App\Services;

use App\Repositories\PhysicalActivitySportRepository;
use App\Exceptions\FailedAction;
use Illuminate\Http\Response;

class PhysicalActivitySportService
{
    private $physicalActivitySportRepository;

    public function __construct(PhysicalActivitySportRepository $physicalActivitySportRepository)
    {
        $this->physicalActivitySportRepository = $physicalActivitySportRepository;
    }

    public function find(int $id)
    {
        try {
            $physicalActivitySport = $this->physicalActivitySportRepository->find($id);
            return $physicalActivitySport;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get sport. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAll(array $filters)
    {
        try {
            $physicalActivitySports = $this->physicalActivitySportRepository->getAll($filters);
            return $physicalActivitySports;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get sports. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function create(array $data)
    {
        try {
            $physicalActivitySport = $this->physicalActivitySportRepository->create($data);
            return $physicalActivitySport;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to create sport. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $physicalActivitySport = $this->physicalActivitySportRepository->update($id, $data);
            return $physicalActivitySport;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to update sport. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $physicalActivitySport = $this->physicalActivitySportRepository->delete($id);
            return $physicalActivitySport;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to delete sport. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
