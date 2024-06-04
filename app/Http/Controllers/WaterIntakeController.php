<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaterIntakeRequest;
use App\Http\Requests\GetWaterIntakeRequest;
use App\Services\WaterIntakeService;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Inertia\Inertia;

class WaterIntakeController extends Controller
{

    private $waterIntakeService;

    public function __construct(WaterIntakeService $waterIntakeService)
    {
        // $this->middleware('auth:api');
        $this->waterIntakeService = $waterIntakeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(GetWaterIntakeRequest $request)
    {
        $data = $request->validated();
        $initialDate = isset($data['initial_date']) && $data['initial_date'] ? $data['initial_date'] : null;
        $finalDate = isset($data['final_date']) && $data['final_date'] ? $data['final_date'] : null;
        $amount = isset($data['amount']) && $data['amount'] ? $data['amount'] : null;
        $page = isset($data['page']) && $data['page'] ? $data['page'] : 1;
        $perPage = isset($data['per_page']) && $data['per_page'] ? $data['per_page'] : 10;

        $qb = auth()->user()->WaterIntake()
            ->when($initialDate, function ($query) use ($initialDate) {
                return $query->whereDate('created_at', '>=', $initialDate);
            })
            ->when($finalDate, function ($query) use ($finalDate) {
                return $query->whereDate('created_at', '<=', $finalDate);
            })
            ->when($amount, function ($query) use ($amount) {
                return $query->where('amount', $amount);
            });

        $chartQb = $qb;
        $totalAmount = $qb->sum('amount');
        $waterIntakes = $qb->paginate($perPage);

        $waterIntakeChartData = [];
        $waterIntakeChartData = $chartQb->selectRaw('DATE(created_at) as date, SUM(amount) as total_amount')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    Carbon::parse($item->date)->format('d/m/Y'),
                    (float)$item->total_amount,
                    auth()->user()->daily_water_amount
                ];
            });

        $waterIntakeChartData = array_merge([["Dia", "Consumo de água", "Meta"]], $waterIntakeChartData->toArray());

        // return response()->json([
        //     'status' => 'success',
        //     'total_amount' => $totalAmount,
        //     'water_intake_list' => $waterIntakes,
        //     'water_intake_chart' => $waterIntakeChartData
        // ]);

        return Inertia::render('WaterIntake/Index', [
            'waterIntakes' => $waterIntakes,
            'waterIntakeChartData' => $waterIntakeChartData,
            'totalAmount' => $totalAmount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterIntakeRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id ?? $request->user_id;
        $response = $this->waterIntakeService->create($data);

        if ($response) {
            $status = isset($response['success']) ? 'success' : 'error';
            $json = [
                'status' => $status,
                'message' => $response[$status],
            ];
            if ($status === 'success') {
                $json['water_intake'] = $response['data'];
            }
        } else {
            $json = [
                'status' => 'error',
                'message' => 'Erro ao salvar o consumo de água',
            ];
        }

        return to_route('dashboard')->with([
            'message' => $json['message'],
            'type' => $json['status'],
            'title' => $json['status'] === 'success' ? 'Successo' : 'Erro',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $waterIntake = $this->waterIntakeService->delete($id);

        if ($waterIntake) {
            return redirect('dashboard')->with([
                'message' => __('Consumo de água deletado com sucesso'),
                'type' => 'success',
                'title' => 'Successo',
            ]);
        }

        return redirect('dashboard')->with([
            'message' => __('Erro ao deletar o consumo de água'),
            'type' => 'error',
            'title' => 'Erro',
        ]);
    }

    public function getWaterIntakesByDay(GetWaterIntakeRequest $request)
    {
        $data = $request->validated();

        $date = isset($data['date']) && $data['date'] ? $data['date'] : now()->toDateString();
        $userId = auth()->user()->id;

        $waterIntakes = $this->waterIntakeService->getWaterIntakesByDay($userId, $date);
        $totalAmount = $waterIntakes->sum('amount');

        return response()->json([
            'status' => 'success',
            'total_amount' => $totalAmount,
            'water_intake_list' => $waterIntakes,
        ]);
    }
}
