<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Services\WaterIntakeService;
use App\Services\WeightControlService;
use App\Services\PhysicalActivityService;
use Carbon\Carbon;

class DashboardController extends Controller
{

    private $waterIntakeService;
    private $weightControlService;
    private $physicalActivityService;
    private $user;

    public function __construct(
        WaterIntakeService $waterIntakeService,
        WeightControlService $weightControlService,
        PhysicalActivityService $physicalActivityService
    ) {
        $this->waterIntakeService = $waterIntakeService;
        $this->weightControlService = $weightControlService;
        $this->physicalActivityService = $physicalActivityService;
    }

    public function index()
    {
        $this->user = auth()->user();
        $waterIntakes = $this->waterIntakeService->getWaterIntakesByDay($this->user->id, Carbon::now()->toDateString());
        $weeklyWaterIntakeChartData = $this->waterIntakeService->getThisWeekWaterIntakeChartData($this->user->id, $this->user->daily_water_amount ?? 2000);
        $monthlyWaterIntakeChartData = $this->waterIntakeService->getThisMonthWaterIntakeChartData($this->user->id, $this->user->daily_water_amount ?? 2000);
        $weightControls = $this->weightControlService->getWeightControlsByUser($this->user->id, 5);
        $thisYearBodyWeightVariationChartData = $this->weightControlService->getThisYearBodyWeightVariationChartData($this->user->id);
        $physicalActivities = $this->physicalActivityService->getPhysicalActivitiesByWeek($this->user->id, Carbon::now()->toDateString());
        $waterIntakeContainers = auth()->user()->waterIntakeContainers;
        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => $this->user,
                'waterIntakes' => $waterIntakes['water_intakes']->toArray(request()),
                'totalWaterIntake' => $waterIntakes['total_water_intake'],
                'weeklyWaterIntakeChartData' => $weeklyWaterIntakeChartData,
                'monthlyWaterIntakeChartData' => $monthlyWaterIntakeChartData,
                'weightControls' => $weightControls,
                'thisYearBodyWeightVariationChartData' => $thisYearBodyWeightVariationChartData,
                'physicalActivities' => $physicalActivities,
                'waterIntakeContainers' => $waterIntakeContainers,
            ]
        ]);
    }
}
