import React from 'react';

export interface WaterIntake {
    id: number;
    amount: number;
    user_id: number;
    created_at: string;
    updated_at: string;
}

const Index: React.FC<{ waterIntakes: WaterIntake[], totalAmount: number }> = ({ waterIntakes, totalAmount }) => {
    return (
        <div className="py-12">
            <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div className="p-6 text-gray-900">
                        <div className="flex items-center justify-between">
                            <div>
                                <h1 className="text-2xl font-semibold">Consumo de água hoje</h1>
                                {/*auth.user.waterIntakeToday é um array com o consumo de agua de hoje. mapear consumo de agua montando uma tabela */}
                                {waterIntakes && waterIntakes.map((waterIntake, index) => (
                                    <p key={index} className="text-gray-600">
                                        Você consumiu {waterIntake.amount} litros de água hoje.
                                    </p>
                                ))}
                                <p className="text-gray-600">Você consumiu {totalAmount} de água hoje.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Index;
