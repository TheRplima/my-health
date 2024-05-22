<?php

namespace App\Services;

use App\Repositories\PhysicalActivityCategoryRepository;
use App\Repositories\PhysicalActivitySportRepository;
use App\Repositories\PhysicalActivityRepository;
use App\Services\PhysicalActivityCategoryService;
use App\Services\PhysicalActivitySportService;
use App\Exceptions\FailedAction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;

class PhysicalActivityService
{
    private $physicalActivityRepository;
    private $physicalActivitySportService;
    private $physicalActivityCategoryService;

    public function __construct(PhysicalActivityRepository $physicalActivityRepository)
    {
        $this->physicalActivityRepository = $physicalActivityRepository;
        $this->physicalActivitySportService = new PhysicalActivitySportService(new PhysicalActivitySportRepository());
        $this->physicalActivityCategoryService = new PhysicalActivityCategoryService(new PhysicalActivityCategoryRepository());
    }

    //get all physical activities from user, allowing filter by date range, category and sport
    public function getAll(int $userId, array $filters)
    {
        try {
            $physicalActivities = $this->physicalActivityRepository->getAll($userId, $filters);
            return $physicalActivities;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get physical activities. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function create(array $data)
    {
        try {
            $user = isset($data['user_id']) ? User::find($data['user_id']) : auth()->user();

            $startTime = strtotime($data['start_time']);
            $endTime = strtotime($data['end_time']);
            $durationInMinutes = ($endTime - $startTime) / 60;
            $durationInHours = $durationInMinutes / 60;

            $data['user_id'] = $user->id;
            $data['duration'] = round($durationInHours, 2);
            $data['date'] = date('Y-m-d', strtotime($data['date']));

            //if all needed params to calculate the calories burned are set, calculate it using the function below
            if (isset($data['sport_id']) && isset($data['effort_level']) && isset($data['duration']) && $user->dob && $user->weight && $user->height && $user->gender) {
                $data['calories_burned'] = $this->getCaloriesBurned($data);
            }

            $physicalActivity = $this->physicalActivityRepository->create($data);

            return $physicalActivity;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to create physical activity. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getCaloriesBurned(array $data)
    {
        $user = isset($data['user_id']) ? User::find($data['user_id']) : auth()->user();
        $sport = $this->physicalActivitySportService->find($data['sport_id']);

        $effortMultiplier = 1;
        if ($data['effort_level'] === 'low') {
            $effortMultiplier = 0.75;
        } elseif ($data['effort_level'] === 'high') {
            $effortMultiplier = 1.25;
        }
        $adjustedMet = $sport->metabolic_equivalent * $effortMultiplier;
        $bmr = $this->calculateBMR($user);
        $caloriesBurned = ($adjustedMet * $bmr / 24) * $data['duration'];

        return round($caloriesBurned, 2);
    }

    private function calculateBMR($user)
    {
        $userAge = Carbon::parse($user->dob)->diffInYears(Carbon::now());
        if (strtolower($user->gender) == 'm') {
            return 88.362 + (13.397 * $user->weight) + (4.799 * $user->height) - (5.677 * $userAge);
        } else {
            return 447.593 + (9.247 * $user->weight) + (3.098 * $user->height) - (4.330 * $userAge);
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $physicalActivity = $this->physicalActivityRepository->update($id, $data);
            return $physicalActivity;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to update physical activity. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function delete(int $id)
    {
        try {
            $physicalActivity = $this->physicalActivityRepository->delete($id);
            return $physicalActivity;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to delete physical activity. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getCategoryOptions()
    {
        try {
            $categories = $this->physicalActivityCategoryService->getAll();
            return $categories;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get categories. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getSportOptions($category_id)
    {
        try {
            $filters = $category_id != 0 ? ['category_id' => $category_id] : [];
            $sports = $this->physicalActivitySportService->getAll($filters);
            return $sports;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get sports. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getEffortLevels($toDb = false)
    {
        if ($toDb) {
            return [
                1 => 'low',
                2 => 'medium',
                3 => 'high'
            ];
        }
        return [
            1 => 'Baixo',
            2 => 'Médio',
            3 => 'Alto'
        ];
    }

    //get category related to sport of the physical activity
    public function getCategoryBySport(int $sportId)
    {
        try {
            $sport = $this->physicalActivitySportService->find($sportId);
            $category = $this->physicalActivityCategoryService->find($sport->category_id);
            return $category;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get category by sport. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getCategoryByActivity(int $activityId)
    {
        try {
            $activity = $this->physicalActivityRepository->find($activityId);
            $category = $this->physicalActivityCategoryService->find($activity->sport->category_id);
            return $category;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get category by activity. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getCategoriesByActivities($activities)
    {
        $categories = [];
        foreach ($activities as $activity) {
            $category = $this->getCategoryByActivity($activity->id);
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }

        return $categories;
    }

    public function showCategoryOptions($error_msg = false)
    {
        try {
            $categories = $this->getCategoryOptions();
            $categoryOptions = '';
            foreach ($categories as $category) {
                $categoryOptions .= "{$category->id}. {$category->name}\n";
            }

            if ($error_msg) {
                return "Categoria inválida. Selecione uma categoria válida:\n" . $categoryOptions;
            }

            return "Selecione a categoria da atividade física:\n" . $categoryOptions;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get category options. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function showSportOptions($category_id, $error_msg = false)
    {
        try {
            $sports = $this->getSportOptions($category_id);
            $sportOptions = '';
            foreach ($sports as $sport) {
                $sportOptions .= "{$sport->id}. {$sport->name}\n";
            }

            if ($error_msg) {
                return "Esporte inválido. Selecione um esporte válido:\n" . $sportOptions;
            }

            return "Selecione o esporte da atividade física:\n" . $sportOptions;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get sport options. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function showEffortLevelsOptions($error_msg = false)
    {
        try {
            $effortLevels = $this->getEffortLevels();
            $effortOptions = '';
            foreach ($effortLevels as $key => $value) {
                $effortOptions .= "{$key}. {$value}\n";
            }

            if ($error_msg) {
                return "Nível de esforço inválido. Selecione um nível de esforço válido:\n" . $effortOptions;
            }

            return "Selecione o nível de esforço da atividade física:\n" . $effortOptions;
        } catch (\Exception $e) {
            throw new FailedAction('Failed to get effort levels. Error: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function createFromBot(array $data)
    {
        $physicalActivity = $this->create($data);

        $text = "Atividade física registrada com sucesso!\nVocê queimou *{$physicalActivity->calories_burned}* calorias.";
        if ($physicalActivity->calories_burned < 100) {
            $text .= "\n\nIsso é menos do que uma fatia de pizza! 🍕\nVocê precisa se esforçar mais!";
        } elseif ($physicalActivity->calories_burned < 400) {
            $text .= "\n\nIsso é equivalente a uma refeição média! 🍲\nMuito bem!";
        } else {
            $text .= "\n\nIsso é equivalente a um banquete! 🍽️\nParabéns pelo esforço!";
        }

        return $text;
    }

    public function showAllFromBot(int $userId, array $filters)
    {
        $physicalActivities = $this->getAll($userId, $filters)->sortBy('date');
        $dates = $physicalActivities->pluck('date')->unique();
        $categories = $this->getCategoriesByActivities($physicalActivities);

        $text = "";
        foreach ($dates as $date) {
            $text .= "*{$date}*:\n";
            foreach ($categories as $category) {
                if (!$physicalActivities->where('date', $date)->where('sport.category_id', $category->id)->count()) {
                    continue;
                }
                $text .= "- {$category->name}:\n";
                $sports = $this->getSportOptions($category->id);
                foreach ($sports as $sport) {
                    if (!$physicalActivities->where('date', $date)->where('sport_id', $sport->id)->count()) {
                        continue;
                    }
                    $text .= "-- {$sport->name}:\n";
                    $activities = $physicalActivities->where('date', $date)->where('sport_id', $sport->id);
                    $text .= $this->formatPhysicalActivities($activities);
                }
            }
        }

        return $text;
    }

    public function formatPhysicalActivities($physicalActivities)
    {
        //loop at the activities and format them to be shown in the bot, but i want 10 activities per message, so i need to split the activities in groups of 10
        $activitiesText = [];
        $activitiesCount = $physicalActivities->count();
        $activities = $physicalActivities->values();
        $activitiesGroups = $activities->chunk(10);
        foreach ($activitiesGroups as $key => $activitiesGroup) {
            $text = '';
            foreach ($activitiesGroup as $activity) {
                $startTime = date('H:i', strtotime($activity->start_time));
                $endTime = date('H:i', strtotime($activity->end_time));
                $text .= "- *Atividade:* {$activity->name}\n- *Início:* {$startTime} - *Fim:* {$endTime}\n- *Duração:* {$activity->duration}h\n- *Gasto calórico:* {$activity->calories_burned} kcal\n\n";
            }
            $activitiesText[] = $text;
        }

        return $activitiesText;
    }

    public function showPhysicalActivitiesForThisWeek(int $userId)
    {
        $filters = [
            'start_date' => date('Y-m-d', strtotime('sunday last week')),
            'end_date' => date('Y-m-d', strtotime('saturday this week'))
        ];

        $text = $this->showAllFromBot($userId, $filters);

        return $text;
    }

    public function showPhysicalActivitiesForThisMonth(int $userId)
    {
        $filters = [
            'start_date' => date('Y-m-01'),
            'end_date' => date('Y-m-t')
        ];

        $text = $this->showAllFromBot($userId, $filters);

        return $text;
    }

    public function getSummary(int $userId, array $filters)
    {
        $physicalActivities = $this->getAll($userId, $filters);
        $countPhysicalActivities = $physicalActivities->count();

        if (!$countPhysicalActivities) {
            return "Nenhuma atividade física registrada neste período.";
        }

        $totalCaloriesBurned = $physicalActivities->sum('calories_burned');
        $totalCaloriesBurned = round($totalCaloriesBurned, 2);
        $totalDuration = $physicalActivities->sum('duration');
        $daysWorkedOut = $physicalActivities->pluck('date')->unique()->count();
        $daysWorkedOut = $daysWorkedOut > 1 ? "{$daysWorkedOut} dias" : "{$daysWorkedOut} dia";
        $dayMoreWorkedOut = $physicalActivities->groupBy('date')->sortByDesc(function ($group) {
            return $group->count();
        })->keys()->first();

        $totalCaloriesBurnedDayMoreWorkedOut = $physicalActivities->where('date', $dayMoreWorkedOut)->sum('calories_burned');
        $totalCaloriesBurnedDayMoreWorkedOut = round($totalCaloriesBurnedDayMoreWorkedOut, 2);
        $dayMoreWorkedOut = date('d/m', strtotime($dayMoreWorkedOut));

        $return = [];
        $text = "Você praticou um total de *{$countPhysicalActivities}* atividades físicas em *{$daysWorkedOut}* dias neste período.\n";
        $text .= "O dia que você mais praticou atividades foi *{$dayMoreWorkedOut}* e queimou um total de *{$totalCaloriesBurnedDayMoreWorkedOut}* calorias.\n";
        $text .= "Total de calorias queimadas no período: *{$totalCaloriesBurned}* Kcal\n";
        $text .= "Total de horas de atividades no período: *{$totalDuration}* h";
        $return[] = $text;
        if ($countPhysicalActivities) {
            $text = "Atividades registradas:\n";
            $activitiesText = $this->formatPhysicalActivities($physicalActivities);
            if (is_array($activitiesText)) {
                $text .= $activitiesText[0];
                $return[] = $text;
                foreach ($activitiesText as $key => $activityText) {
                    if ($key == 0) {
                        continue;
                    }
                    $return[] = "*Continuação...*\n\n" . $activityText;
                }
            } else {
                $text .= $activitiesText;
            }
        }

        return $return;
    }

    public function showWeeklySummary(int $userId)
    {
        $filters = [
            'start_date' => date('Y-m-d', strtotime('sunday last week')),
            'end_date' => date('Y-m-d', strtotime('saturday this week'))
        ];

        $text = $this->getSummary($userId, $filters);

        return $text;
    }

    public function showMonthlySummary(int $userId)
    {
        $filters = [
            'start_date' => date('Y-m-01'),
            'end_date' => date('Y-m-t')
        ];

        $text = $this->getSummary($userId, $filters);

        return $text;
    }
}
