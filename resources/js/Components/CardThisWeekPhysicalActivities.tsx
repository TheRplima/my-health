import React from 'react'

import { router } from '@inertiajs/react'

import PrimaryButton from '../Components/PrimaryButton';
import { FiTrash } from 'react-icons/fi';
import { Card, Table } from 'react-bootstrap';

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

interface PageProps {
    user: User;
    physicalActivities: PhysicalActivity[];
}

const CardThisWeekPhysicalActivities: React.FC<PageProps> = ({ user, physicalActivities }) => {
    function deletePhysicalActivity(id: number) {
        router.delete(`/physical-activity/${id}`);
    }

    if (!physicalActivities || physicalActivities.length <= 0) {
        return (
            <Card className="mb-3 w-100">
                <Card.Header className='d-flex'>
                    <Card.Title>Atividades Físicas da Semana</Card.Title>
                </Card.Header>
                <Card.Body>
                    <Card.Subtitle className="mb-3 text-muted">Você não praticou nenhuma atividade física nesta semana.</Card.Subtitle>
                </Card.Body>
            </Card>
        )
    }

    const countPhysicalActivities = physicalActivities.length;
    const countPhysicalActivitiesLabel = countPhysicalActivities > 1 ? `${countPhysicalActivities} atividades físicas` : `${countPhysicalActivities} atividade física`;
    const daysWorkedOut = physicalActivities.map(physicalActivity => physicalActivity.date).filter((value, index, self) => self.indexOf(value) === index).length;
    const daysWorkedOutLabel = daysWorkedOut > 1 ? `${daysWorkedOut} dias` : `${daysWorkedOut} dia`;
    const dayMoreWorkedOut = physicalActivities.map(physicalActivity => physicalActivity.date).reduce((acc: { [key: string]: number }, date) => {
        acc[date] = (acc[date] || 0) + 1;
        return acc;
    }, {});
    const dayMoreWorkedOutLabel = new Date(Object.keys(dayMoreWorkedOut).reduce((a, b) => dayMoreWorkedOut[a] > dayMoreWorkedOut[b] ? a : b) + 'T00:00:00').toLocaleDateString('pt-BR');
    const totalCaloriesBurnedDayMoreWorkedOut = physicalActivities.filter(physicalActivity => new Date(physicalActivity.date + 'T00:00:00').toLocaleDateString('pt-BR') === dayMoreWorkedOutLabel).reduce((acc, physicalActivity) => acc + physicalActivity.calories_burned, 0);

    const totalCaloriesBurned = physicalActivities.reduce((acc, physicalActivity) => acc + physicalActivity.calories_burned, 0);
    const totalDuration = physicalActivities.reduce((acc, physicalActivity) => acc + physicalActivity.duration, 0);

    return (
        <Card className="mb-3 w-100">
            <Card.Header className='d-flex'>
                <Card.Title>Atividades Físicas da Semana</Card.Title>
            </Card.Header>
            <Card.Body>
                <Card.Subtitle className="mb-3 text-muted">Você praticou um total de <strong>{countPhysicalActivitiesLabel}</strong> em <strong>{daysWorkedOutLabel}</strong> nesta semana.</Card.Subtitle>
                <Card.Subtitle className="mb-3 text-muted">O dia que você mais praticou atividades foi <strong>{dayMoreWorkedOutLabel}</strong> e queimou um total de <strong>{totalCaloriesBurnedDayMoreWorkedOut.toFixed(2)}</strong> calorias.</Card.Subtitle>
                <Card.Subtitle className="mb-3 text-muted">Total de calorias queimadas no período: <strong>{totalCaloriesBurned.toFixed(2)}Kcal</strong></Card.Subtitle>
                <Card.Subtitle className="mb-3 text-muted">Total de horas de atividades no período: <strong>{totalDuration}h</strong></Card.Subtitle>
                <Card.Subtitle className="mb-3 text-muted">Média de calorias queimadas por atividade: <strong>{(totalCaloriesBurned / countPhysicalActivities).toFixed(2)}Kcal</strong></Card.Subtitle>
                <Card.Subtitle className="mb-3 text-muted">Média de calorias queimadas por dia: <strong>{(totalCaloriesBurned / daysWorkedOut).toFixed(2)}Kcal</strong></Card.Subtitle>
                <Table hover responsive variant="light">
                    <thead className="light">
                        <tr>
                            <th scope="col" className='text-center'>Data</th>
                            <th scope="col" className='text-center'>Início</th>
                            <th scope="col" className='text-center'>Duração</th>
                            <th scope="col" className='text-center'>Nome</th>
                            <th scope="col" className='text-center'>Calorias</th>
                            <th scope="col" className='text-center'>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        {physicalActivities && physicalActivities.map((physicalActivity, index) => (
                            <tr key={index}>
                                <td className='text-center'>{new Date(physicalActivity.date + 'T00:00:00').toLocaleDateString('pt-BR')}</td>
                                <td className='text-center'>{physicalActivity.start_time}</td>
                                <td className='text-center'>{physicalActivity.duration}h</td>
                                <td className='text-center'>{physicalActivity.name}</td>
                                <td className='text-center'>{physicalActivity.calories_burned}kcal</td>
                                <td className='text-center'>
                                    <PrimaryButton onClick={
                                        () => {
                                            if (confirm('Tem certeza que deseja excluir este registro?')) {
                                                deletePhysicalActivity(physicalActivity.id);
                                            }
                                        }
                                    }><FiTrash /></PrimaryButton>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </Table>
            </Card.Body>
        </Card>
    )
}

export default CardThisWeekPhysicalActivities
