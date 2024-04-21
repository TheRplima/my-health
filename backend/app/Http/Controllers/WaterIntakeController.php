<?php

namespace App\Http\Controllers;

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
    public function index(Request $request)
    {
        $request->validate([
            'initial_date' => ['nullable', 'date', 'required_with:final_date', 'filled'],
            'final_date' => ['nullable', 'date', 'required_with:initial_date', 'filled', 'after_or_equal:initial_date']
        ]);

        if ($request->has('initial_date') && $request->has('final_date')) {
            $initialDate = Carbon::createFromFormat('Y-m-d', $request->get('initial_date'));
            $finalDate = Carbon::createFromFormat('Y-m-d', $request->get('final_date'));
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
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric'
        ]);

        $waterIntake = WaterIntake::create([
            'user_id' => auth()->user()->id ?? $request->user_id,
            'amount' => $request->amount,
        ]);

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

    public function getWaterIntakesByDay(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'date', 'filled']
        ]);

        $date = $request->has('date') ? $request->get('date') : now()->toDateString();
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
