<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterIngestion;
use Carbon\Carbon;

class WaterIngestionController extends Controller
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
            $waterIngestions = auth()->user()->WaterIngestion()
                ->whereDate('created_at', ">=", $initialDate)
                ->whereDate('created_at', "<=", $finalDate)
                ->get();
        }else{
            $waterIngestions = auth()->user()->WaterIngestion()->get();
        }

        $totalAmount = 0;
        foreach ($waterIngestions as $waterIngestion) {
            $totalAmount += $waterIngestion->amount;
        }

        return response()->json([
            'status' => 'success',
            'total_amount' => $totalAmount,
            'water_ingestion_list' => $waterIngestions,
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

        $waterIngestion = WaterIngestion::create([
            'user_id' => auth()->user()->id ?? $request->user_id,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Water Ingestion created successfully',
            'waterIngestion' => $waterIngestion,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $waterIngestion = WaterIngestion::find($id);
        $waterIngestion->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Water Ingestion deleted successfully',
            'waterIngestion' => $waterIngestion,
        ]);
    }

    public function getWaterIngestionsByDay(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'date', 'filled']
        ]);

        $date = $request->has('date') ? $request->get('date') : now()->toDateString();
        $waterIngestions = auth()->user()->WaterIngestion()
            ->whereDate('created_at', $date)
            ->get();

        $totalAmount = 0;
        foreach ($waterIngestions as $waterIngestion) {
            $totalAmount += $waterIngestion->amount;
        }

        return response()->json([
            'status' => 'success',
            'totalAmount' => $totalAmount,
            'waterIngestions' => $waterIngestions,
        ]);
    }
}
