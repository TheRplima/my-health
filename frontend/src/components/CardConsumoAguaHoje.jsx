import React, { useState, useEffect } from 'react'

import { useAuth } from "../hooks/auth";

import useWaterIntakeData from '../services/useWaterIntakeData'
import RegisterWaterIntakeModal from './RegisterWaterIntakeModal';
import { confirm } from "./ConfirmationModal";

import Card from 'react-bootstrap/Card';
import ProgressBar from 'react-bootstrap/ProgressBar'
import { FiTrash } from 'react-icons/fi';
import Button from 'react-bootstrap/Button';
import Spinner from 'react-bootstrap/Spinner';

const CardConsumoAguaHoje = () => {
    const [amount, setAmount] = useState(0);
    const [waterIntakes, setWaterIntakes] = useState(null);
    const [waterIntakesTotalAmount, setWaterIntakesTotalAmount] = useState(null);
    const [loading, setLoading] = useState(true);
    const { cookies } = useAuth();
    const userProfileData = cookies.user
    const { setWaterIntakeData, deleteWaterIntake } = useWaterIntakeData()

    const handleRegisterWaterIntake = async (e) => {
        setWaterIntakeData(amount)
    }

    const handleDeleteButtonClick = (id) => {
        confirm('Deseja realmente excluir este registro?', 'Remover registro', 'Sim', 'Não').then(
            (response) => {
                if (response) {
                    deleteWaterIntake(id)
                }
            })
    }

    useEffect(() => {
        async function loadStorageData() {
            const storageWaterIntakes = cookies.water_intakes;

            if (storageWaterIntakes) {
                setWaterIntakes(storageWaterIntakes.list);
                setWaterIntakesTotalAmount(storageWaterIntakes.total_amount);
                setLoading(false);
            }
        }

        loadStorageData();

        return () => {
            setWaterIntakes(null);
            setWaterIntakesTotalAmount(null);
            setLoading(true);
        }
    }, [cookies.water_intakes]);


    return (
        <>
            <Card className="mb-3">
                <Card.Header className='d-flex'>
                    <Card.Title>Controle de Água</Card.Title>
                    <RegisterWaterIntakeModal handleRegisterWaterIntake={handleRegisterWaterIntake} setAmount={setAmount} amount={amount} />
                </Card.Header>
                {(!loading) ? (
                    <Card.Body>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Meta diária:</strong> {userProfileData.daily_water_amount} ml</Card.Subtitle>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Total consumido hoje:</strong> {waterIntakesTotalAmount} ml</Card.Subtitle>
                        <ProgressBar animated now={waterIntakesTotalAmount} max={userProfileData.daily_water_amount} label={`${((waterIntakesTotalAmount / userProfileData.daily_water_amount) * 100).toFixed(2)}%`} />
                        <table className="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" className='text-center'>Data</th>
                                    <th scope="col" className='text-center'>Quantidade</th>
                                    <th scope="col" className='text-center'>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {waterIntakes.length > 0 ? (
                                    waterIntakes.map((waterIntake, index) => (
                                        <tr key={index}>
                                            <td className='text-center'>{new Date(waterIntake.created_at).toLocaleTimeString('pt-BR')}</td>
                                            <td className='text-center'>{waterIntake.amount} ml</td>
                                            <td className='text-center'>
                                                <Button variant="danger" onClick={() => handleDeleteButtonClick(waterIntake.id)}><FiTrash /></Button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="3" className='text-center'>
                                            <Card.Text>Nenhum registro encontrado</Card.Text>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </Card.Body>
                ) : (
                    <Card.Body className='text-center'>
                        <Spinner animation="border" role="status">
                            <span className="sr-only">Loading...</span>
                        </Spinner>
                    </Card.Body>
                )}
            </Card>
        </>
    )
}

export default CardConsumoAguaHoje