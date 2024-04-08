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
    public function index()
    {
        $waterIngestions = WaterIngestion::all();
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
     * Display the specified resource.
     */
    public function show($id)
    {
        $waterIngestion = WaterIngestion::find($id);
        return response()->json([
            'status' => 'success',
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
