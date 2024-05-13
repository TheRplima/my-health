<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWaterIntakeContainerRequest;
use App\Http\Requests\StoreWaterIntakeContainerRequest;
use App\Services\WaterIntakeContainerService;
use App\Models\WaterIntakeContainer;
use Illuminate\Http\Response;

class WaterIntakeContainerController extends Controller
{


    private $waterIntakeContainerService;

    public function __construct(WaterIntakeContainerService $waterIntakeContainerService)
    {
        $this->middleware('auth:api');
        $this->waterIntakeContainerService = $waterIntakeContainerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $waterIntakeContainers = $this->waterIntakeContainerService->getWaterIntakeContainersByUser(auth()->user()->id);

        return response()->json([
            'status' => 'success',
            'water_intake_container_list' => $waterIntakeContainers,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterIntakeContainerRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id ?? $request->user_id;
        $waterIntakeContainer = $this->waterIntakeContainerService->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Water Intake container created successfully',
            'water_intake_container' => $waterIntakeContainer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateWaterIntakeContainerRequest $request, WaterIntakeContainer $waterIntakeContainer)
    {
        $data = $request->validated();
        $waterIntakeContainer = $this->waterIntakeContainerService->update($id, $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Water Intake container updated successfully',
            'water_intake_container' => $waterIntakeContainer,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $waterIntakeContainer = $this->waterIntakeContainerService->delete($id);

        if ($waterIntakeContainer) {
            return response()->json([
                'status' => 'success',
                'message' => 'Water Intake container deleted successfully',
                'water_intake_container' => $waterIntakeContainer,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Water Intake container not found',
        ], Response::HTTP_NOT_FOUND);
    }
}
