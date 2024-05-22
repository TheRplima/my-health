<?php

namespace App\Services;

use App\Repositories\PhysicalActivityCategoryRepository;
use App\Exceptions\FailedAction;
use Illuminate\Http\Response;

class PhysicalActivityCategoryService
{
    private $physicalActivityCategoryRepository;

    public function __construct(PhysicalActivityCategoryRepository $physicalActivityCategoryRepository)
    {
        $this->physicalActivityCategoryRepository = $physicalActivityCategoryRepository;
    }

    public function find(int $id)
    {
        try {
            $physicalActivityCategory = $this->physicalActivityCategoryRepository->find($id);
            return $physicalActivityCategory;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get category. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAll()
    {
        try {
            $physicalActivityCategories = $this->physicalActivityCategoryRepository->getAll();
            return $physicalActivityCategories;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get categories. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function create(array $data)
    {
        try {
            $physicalActivityCategory = $this->physicalActivityCategoryRepository->create($data);
            return $physicalActivityCategory;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to create category. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $physicalActivityCategory = $this->physicalActivityCategoryRepository->update($id, $data);
            return $physicalActivityCategory;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to update category. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $physicalActivityCategory = $this->physicalActivityCategoryRepository->delete($id);
            return $physicalActivityCategory;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to delete category. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
