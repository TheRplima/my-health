import React, { useState } from 'react'
import { router } from '@inertiajs/react'
import { FiTrash } from 'react-icons/fi';
import Card from 'react-bootstrap/Card';
import ProgressBar from 'react-bootstrap/ProgressBar';
import Table from 'react-bootstrap/Table';
import Button from 'react-bootstrap/Button';

import RegisterWaterIntakeModal from './RegisterWaterIntakeModal';

interface User {
    id: number;
    name: string;
    email: string;
    weight: number;
    height: number;
    birth_date: string;
    daily_water_amount: number;
    email_verified_at: string;
}

export interface WaterIntake {
    id: number;
    amount: number;
    user_id: number;
    created_at: string;
    updated_at: string;
}

interface PageProps {
    user: User;
    waterIntakes: WaterIntake[];
    totalWaterIntake: number;
}

const CardWaterIntakeToday: React.FC<PageProps> = ({ user, waterIntakes, totalWaterIntake }) => {
    const [amount, setAmount] = useState<number | string>(0);

    function handleRegisterWaterIntake() {
        router.post('/water-intake', {
            amount: amount
        });
    }

    function deleteWaterIntake(id: number) {
        router.delete(`/water-intake/${id}`);
    }

    return (
        <Card className="mb-3 w-100">
            <Card.Header className='d-flex'>
                <Card.Title>Consumo de Água Hoje</Card.Title>
                <RegisterWaterIntakeModal handleRegisterWaterIntake={handleRegisterWaterIntake} setAmount={setAmount} amount={amount} />
            </Card.Header>
            <Card.Body>
                <Card.Subtitle className="mb-3 text-muted"><strong>Meta diária:</strong> {user.daily_water_amount / 1000} litros</Card.Subtitle>
                <Card.Subtitle className="mb-3 text-muted">Você consumiu <strong>{totalWaterIntake / 1000} litros</strong> de água hoje.</Card.Subtitle>
                <ProgressBar animated now={totalWaterIntake} max={user.daily_water_amount} label={totalWaterIntake > 0 ? `${((totalWaterIntake / user.daily_water_amount) * 100).toFixed(2)}%` : ''} />
                <Table hover variant="light">
                    <thead>
                        <tr>
                            <th scope="col" className='text-center'>Data</th>
                            <th scope="col" className='text-center'>Quantidade</th>
                            <th scope="col" className='text-center'>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        {waterIntakes && waterIntakes.length > 0 ? (
                            waterIntakes.map((waterIntake, index) => (
                                <tr key={index}>
                                    <td className='text-center'>{new Date(waterIntake.created_at).toLocaleTimeString('pt-BR')}</td>
                                    <td className='text-center'>{waterIntake.amount}ml</td>
                                    <td className='text-center'>
                                        <Button variant="danger" size={'sm'} onClick={
                                            () => {
                                                if (confirm('Tem certeza que deseja excluir este registro?')) {
                                                    deleteWaterIntake(waterIntake.id);
                                                }
                                            }
                                        }><FiTrash /></Button>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td></td>
                                <td className='nodata text-center d-flex align-items-center justify-content-center'>
                                    Nenhum registro encontrado
                                </td>
                                <td></td>
                            </tr>
                        )}
                    </tbody>
                </Table>
            </Card.Body>
        </Card>
    )
}

export default CardWaterIntakeToday
