<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhysicalActivitySportRequest;
use App\Http\Requests\StorePhysicalActivitySportRequest;
use App\Http\Requests\UpdatePhysicalActivitySportRequest;
use App\Services\PhysicalActivitySportService;
use Illuminate\Http\JsonResponse;

class PhysicalActivitySportController extends Controller
{
    private $physicalActivitySportService;

    public function __construct(PhysicalActivitySportService $physicalActivitySportService)
    {
        $this->middleware('auth:api');
        $this->physicalActivitySportService = $physicalActivitySportService;
    }

    //return all sports allowing filter by category using the query parameter category_id, validate request using PhysicalActivitySportRequest
    public function index(PhysicalActivitySportRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $physicalActivitySports = $this->physicalActivitySportService->getAll($filters);
        return response()->json($physicalActivitySports, 200);
    }


    public function store(StorePhysicalActivitySportRequest $request): JsonResponse
    {
        $data = $request->validated();
        $physicalActivitySport = $this->physicalActivitySportService->create($data);
        return response()->json($physicalActivitySport, 201);
    }

    public function update(UpdatePhysicalActivitySportRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $physicalActivitySport = $this->physicalActivitySportService->update($id, $data);
        return response()->json($physicalActivitySport, 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->physicalActivitySportService->delete($id);
        return response()->json(null, 204);
    }
}
