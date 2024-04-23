<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetWaterIntakeRequest;
use App\Http\Requests\StoreWaterIntakeRequest;
use Illuminate\Http\Request;
use App\Models\WaterIntake;
use Carbon\Carbon;

class WaterIntakeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(GetWaterIntakeRequest $request)
    {
        $data = $request->validated();
        $initialDate = isset($data['initial_date']) && $data['initial_date'] ? $data['initial_date'] : null;
        $finalDate = isset($data['final_date']) && $data['final_date'] ? $data['final_date'] : null;
        if ($initialDate && $finalDate) {
            $initialDate = Carbon::createFromFormat('Y-m-d', $initialDate);
            $finalDate = Carbon::createFromFormat('Y-m-d', $finalDate);
            $waterIntakes = auth()->user()->WaterIntake()
                ->whereDate('created_at', ">=", $initialDate)
                ->whereDate('created_at', "<=", $finalDate)
                ->get();
        }else{
            $waterIntakes = auth()->user()->WaterIntake()->get();
        }

        $totalAmount = 0;
        foreach ($waterIntakes as $waterIntake) {
            $totalAmount += $waterIntake->amount;
        }

        return response()->json([
            'status' => 'success',
            'total_amount' => $totalAmount,
            'water_intake_list' => $waterIntakes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterIntakeRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id ?? $request->user_id;
        $waterIntake = WaterIntake::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Water Intake created successfully',
            'water_intake' => $waterIntake,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $waterIntake = WaterIntake::find($id);
        $waterIntake->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Water Intake deleted successfully',
            'water_intake' => $waterIntake,
        ]);
    }

    public function getWaterIntakesByDay(GetWaterIntakeRequest $request)
    {
        $data = $request->validated();

        $date = isset($data['date']) && $data['date'] ? $data['date'] : now()->toDateString();
        $waterIntakes = auth()->user()->WaterIntake()
            ->whereDate('created_at', $date)
            ->get();

        $totalAmount = 0;
        foreach ($waterIntakes as $waterIntake) {
            $totalAmount += $waterIntake->amount;
        }

        return response()->json([
            'status' => 'success',
            'total_amount' => $totalAmount,
            'water_intake_list' => $waterIntakes,
        ]);
    }
}
