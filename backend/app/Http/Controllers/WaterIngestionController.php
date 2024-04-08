<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterIngestion;

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
            $waterIngestions = auth()->user()->WaterIngestion()
                ->whereBetween('created_at', [$request->initial_date, $request->final_date])
                ->get();
            return response()->json([
                'status' => 'success',
                'waterIngestions' => $waterIngestions,
            ]);
        }

        $waterIngestions = auth()->user()->WaterIngestion()->get();
        return response()->json([
            'status' => 'success',
            'waterIngestions' => $waterIngestions,
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
            'message' => 'Todo created successfully',
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
}
