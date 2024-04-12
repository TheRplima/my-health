<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeightControl;
use Carbon\Carbon;

class WeightControlController extends Controller
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
            $weightControls = auth()->user()->weightControl()
                ->whereDate('created_at', ">=", $initialDate)
                ->whereDate('created_at', "<=", $finalDate)
                ->get();
        }else{
            $weightControls = auth()->user()->weightControl()->get();
        }

        return response()->json([
            'status' => 'success',
            'weight_control' => $weightControls,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'weight' => 'required|numeric'
        ]);

        $weightControl = WeightControl::create([
            'user_id' => auth()->user()->id ?? $request->user_id,
            'weight' => $request->weight,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Weight registered successfully',
            'weightControl' => $weightControl,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $weightControl = WeightControl::find($id);
        $weightControl->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Weight register deleted successfully',
            'weightControl' => $weightControl,
        ]);
    }
}
