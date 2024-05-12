<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWeightControlRequest;
use App\Http\Requests\GetWeightControlRequest;
use App\Services\WeightControlService;
use Illuminate\Support\Facades\Auth;
use App\Models\WeightControl;
use Carbon\Carbon;
use Illuminate\Http\Response;

class WeightControlController extends Controller
{

    private $wheightControlService;

    public function __construct(WeightControlService $wheightControlService)
    {
        $this->middleware('auth:api');
        $this->wheightControlService = $wheightControlService;
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
            $weightControls = $this->wheightControlService->getWeightControlsByDateRange(auth()->user()->id, $initialDate, $finalDate, $data['max']);
        } else {
            $weightControls = $this->wheightControlService->getWeightControlsByUser(auth()->user()->id, $data['max']);
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
        $weightControl = $this->wheightControlService->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Weight register created successfully',
            'weight_control' => $weightControl
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $weightControl = $this->wheightControlService->delete($id);

        if ($weightControl) {
            return response()->json([
                'status' => 'success',
                'message' => 'Weight register deleted successfully',
                'weight_control' => $weightControl
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Weight register not found'
        ], Response::HTTP_NOT_FOUND);
    }
}
