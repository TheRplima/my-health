<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhysicalActivityRequest;
use App\Http\Requests\StorePhysicalActivityRequest;
use App\Http\Requests\UpdatePhysicalActivityRequest;
use App\Services\PhysicalActivityService;
use Illuminate\Http\JsonResponse;

class PhysicalActivityController extends Controller
{
    private $physicalActivityService;

    public function __construct(PhysicalActivityService $physicalActivityService)
    {
        $this->middleware('auth:api');
        $this->physicalActivityService = $physicalActivityService;
    }

    //get all physical activities from user, allowing filter by date range, category and sport
    public function index(PhysicalActivityRequest $request): JsonResponse
    {
        $userId = auth()->user()->id;
        $filters = $request->validated();
        $physicalActivities = $this->physicalActivityService->getAll($userId, $filters);
        return response()->json($physicalActivities, 200);
    }


    public function store(StorePhysicalActivityRequest $request): JsonResponse
    {
        $data = $request->validated();
        $physicalActivity = $this->physicalActivityService->create($data);
        return response()->json($physicalActivity, 201);
    }

    public function update(UpdatePhysicalActivityRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $physicalActivity = $this->physicalActivityService->update($id, $data);
        return response()->json($physicalActivity, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->physicalActivityService->delete($id);
        return response()->json(null, 204);
    }
}
