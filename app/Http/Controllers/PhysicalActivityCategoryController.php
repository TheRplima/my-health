<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhysicalActivityCategoryRequest;
use App\Http\Requests\UpdatePhysicalActivityCategoryRequest;
use App\Services\PhysicalActivityCategoryService;
use Illuminate\Http\JsonResponse;

class PhysicalActivityCategoryController extends Controller
{
    private $physicalActivityCategoryService;


    public function __construct(PhysicalActivityCategoryService $physicalActivityCategoryService)
    {
        $this->middleware('auth:api');
        $this->physicalActivityCategoryService = $physicalActivityCategoryService;
    }

    public function index(): JsonResponse
    {
        $physicalActivityCategories = $this->physicalActivityCategoryService->getAll();
        return response()->json($physicalActivityCategories, 200);
    }

    public function store(StorePhysicalActivityCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $physicalActivityCategory = $this->physicalActivityCategoryService->create($data);
        return response()->json($physicalActivityCategory, 201);
    }

    public function update(UpdatePhysicalActivityCategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $physicalActivityCategory = $this->physicalActivityCategoryService->update($id, $data);
        return response()->json($physicalActivityCategory, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->physicalActivityCategoryService->delete($id);
        return response()->json(null, 204);
    }
}
