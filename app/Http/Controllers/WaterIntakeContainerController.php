<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWaterIntakeContainerRequest;
use App\Http\Requests\StoreWaterIntakeContainerRequest;
use App\Services\WaterIntakeContainerService;
use App\Models\WaterIntakeContainer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class WaterIntakeContainerController extends Controller
{


    private $waterIntakeContainerService;

    public function __construct(WaterIntakeContainerService $waterIntakeContainerService)
    {
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
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->user()->id ?? $request->user_id;
            $waterIntakeContainer = $this->waterIntakeContainerService->create($data);

            return redirect('dashboard')->with([
                'message' => __('Water intake container created successfully'),
                'type' => 'success',
                'title' => 'Success',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect('dashboard')->with([
                'message' => __('Error creating water intake container'),
                'type' => 'error',
                'title' => 'Error',
            ]);
        }
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
