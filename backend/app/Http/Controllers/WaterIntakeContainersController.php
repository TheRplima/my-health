<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaterIntakeContainersRequest;
use App\Http\Requests\UpdateWaterIntakeContainersRequest;
use App\Models\WaterIntakeContainers;

class WaterIntakeContainersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $waterIntakeContainers = auth()->user()->WaterIntakeContainers()->get();

        return response()->json([
            'status' => 'success',
            'water_intake_containers' => $waterIntakeContainers,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterIntakeContainersRequest $request)
    {
        $waterIntakeContainer = WaterIntakeContainers::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'size' => $request->size,
            'icon' => $request->icon,
            'active' => $request->active,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Water Intake container created successfully',
            'water_intake' => $waterIntakeContainer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateWaterIntakeContainersRequest $request, WaterIntakeContainers $waterIntakeContainers)
    {
        $waterIntakeContainer = WaterIntakeContainers::find($id);
        $waterIntakeContainer->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Water Intake container updated successfully',
            'water_intake' => $waterIntakeContainer,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $waterIntakeContainer = WaterIntakeContainers::find($id);
        $waterIntakeContainer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Water Intake container deleted successfully',
            'water_intake' => $waterIntakeContainer,
        ]);
    }
}
