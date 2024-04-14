<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeightControl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            'final_date' => ['nullable', 'date', 'required_with:initial_date', 'filled', 'after_or_equal:initial_date'],
            'max' => 'nullable|integer'
        ]);

        if ($request->has('initial_date') && $request->has('final_date')) {
            $initialDate = Carbon::createFromFormat('Y-m-d', $request->get('initial_date'));
            $finalDate = Carbon::createFromFormat('Y-m-d', $request->get('final_date'));
            $weightControls = auth()->user()->weightControl()
                ->whereDate('created_at', ">=", $initialDate)
                ->whereDate('created_at', "<=", $finalDate)
                ->get();
        }else{
            if ($request->has('max')) {
                $user = auth()->user();
                $weightControls = WeightControl::where('user_id',$user->id)->orderBy('created_at','desc')->take($request->get('max'));
                $weightControls = array_reverse($weightControls->get()->toArray());
            }else{
                $weightControls = auth()->user()->weightControl()->get();
            }
        }

        return response()->json([
            'status' => 'success',
            'weight_control_list' => $weightControls,
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

        $user = Auth::user();
        $user->weight = $request->weight;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Weight registered successfully',
            'weight_control' => $weightControl,
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
            'weight_control' => $weightControl,
        ]);
    }
}
