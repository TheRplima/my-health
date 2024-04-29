<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetWeightControlRequest;
use App\Http\Requests\StoreWeightControlRequest;
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
    public function index(GetWeightControlRequest $request)
    {
        $data = $request->validated();
        $initialDate = isset($data['initial_date']) && $data['initial_date'] ? $data['initial_date'] : null;
        $finalDate = isset($data['final_date']) && $data['final_date'] ? $data['final_date'] : null;
        if ($initialDate && $finalDate) {
            $initialDate = Carbon::createFromFormat('Y-m-d', $initialDate);
            $finalDate = Carbon::createFromFormat('Y-m-d', $finalDate);
            $weightControls = auth()->user()->weightControl()
                ->whereDate('created_at', ">=", $initialDate)
                ->whereDate('created_at', "<=", $finalDate)
                ->get();
        } else {
            if (isset($data['max']) && $data['max']) {
                $user = auth()->user();
                $weightControls = WeightControl::where('user_id', $user->id)->orderBy('created_at', 'desc')->take($data['max']);
                $weightControls = array_reverse($weightControls->get()->toArray());
            } else {
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
    public function store(StoreWeightControlRequest $request)
    {
        $data = $request->validated();
        if (isset($data['date']) && $data['date']) {
            $date = Carbon::createFromFormat('Y-m-d', $data['date']);
            $data['created_at'] = $date;
            $data['updated_at'] = $date;
            unset($data['date']);
        }
        $data['user_id'] = auth()->user()->id ?? $request->user_id;

        $weightControl = WeightControl::create($data);

        $user = Auth::user();
        $user->weight = WeightControl::latest()->first()->weight;
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

        $user = Auth::user();
        $latestWeight = WeightControl::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
        if ($latestWeight != null) {
            $user->weight = $latestWeight->weight;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Weight register deleted successfully',
            'weight_control' => $weightControl,
        ]);
    }
}
