export interface User {
    id: number;
    name: string;
    email: string;
    weight: number;
    height: number;
    birth_date: string;
    daily_water_amount: number;
    email_verified_at: string;
    image: string;
}
export interface WaterIntake {
    id: number;
    amount: number;
    user_id: number;
    created_at: string;
    updated_at: string;
}

export interface WaterIntakeChartData {
    date: string;
    amount: number;
    goal: number;
}

export interface WeightControl {
    id: number;
    weight: number;
    user_id: number;
    created_at: string;
    updated_at: string;
}
export interface PhysicalActivity {
    id: number;
    user_id: number;
    name: string;
    description: string;
    sport_id: number;
    calories_burned: number;
    date: string;
    start_time: string;
    end_time: string;
    duration: number;
    effort_level: number;
    created_at: string;
    updated_at: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User,
        waterIntakes: WaterIntake[],
        totalWaterIntake: number,
        weeklyWaterIntakeChartData: WaterIntakeChartData[],
        monthlyWaterIntakeChartData: WaterIntakeChartData[],
        weightControls: WeightControl[],
        thisYearBodyWeightVariationChartData: WeightControl[],
        physicalActivities: PhysicalActivity[],
    };
};
