import React, { useState, useEffect } from 'react'

import { useAuth } from "../hooks/auth";

import useWaterIngestionData from '../services/useWaterIngestionData'
import RegisterWaterIngestionModal from './RegisterWaterIngestionModal';
import { confirm } from "./ConfirmationModal";

import Card from 'react-bootstrap/Card';
import ProgressBar from 'react-bootstrap/ProgressBar'
import { FiTrash } from 'react-icons/fi';
import Button from 'react-bootstrap/Button';
import Spinner from 'react-bootstrap/Spinner';

const CardConsumoAguaHoje = () => {
    const [amount, setAmount] = useState(0);
    const [waterIngestions, setWaterIngestions] = useState(null);
    const [waterIngestionsTotalAmount, setWaterIngestionsTotalAmount] = useState(null);
    const [loading, setLoading] = useState(true);
    const { cookies } = useAuth();
    const userProfileData = cookies.user
    const { setWaterIngestionData, deleteWaterIngestion } = useWaterIngestionData()

    const handleRegisterWaterIngestion = async (e) => {
        setWaterIngestionData(amount)
    }

    const handleDeleteButtonClick = (id) => {
        confirm('Deseja realmente excluir este registro?', 'Remover registro', 'Sim', 'Não').then(
            (response) => {
                if (response) {
                    deleteWaterIngestion(id)
                }
            })
    }

    useEffect(() => {
        async function loadStorageData() {
            const storageWaterIngestions = cookies.water_ingestions;

            if (storageWaterIngestions) {
                setWaterIngestions(storageWaterIngestions.list);
                setWaterIngestionsTotalAmount(storageWaterIngestions.total_amount);
                setLoading(false);
            }
        }

        loadStorageData();

        return () => {
            setWaterIngestions(null);
            setWaterIngestionsTotalAmount(null);
            setLoading(true);
        }
    }, [cookies.water_ingestions]);


    return (
        <>
            <Card className="mb-3">
                <Card.Header className='d-flex'>
                    <Card.Title>Controle de Água</Card.Title>
                    <RegisterWaterIngestionModal handleRegisterWaterIngestion={handleRegisterWaterIngestion} setAmount={setAmount} />
                </Card.Header>
                {(!loading) ? (
                    <Card.Body>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Meta diária:</strong> {userProfileData.daily_water_amount} ml</Card.Subtitle>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Total consumido hoje:</strong> {waterIngestionsTotalAmount} ml</Card.Subtitle>
                        <ProgressBar animated now={waterIngestionsTotalAmount} max={userProfileData.daily_water_amount} label={`${((waterIngestionsTotalAmount / userProfileData.daily_water_amount) * 100).toFixed(2)}%`} />
                        <table className="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" className='text-center'>Data</th>
                                    <th scope="col" className='text-center'>Quantidade</th>
                                    <th scope="col" className='text-center'>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {waterIngestions.length > 0 ? (
                                    waterIngestions.map((waterIngestion, index) => (
                                        <tr key={index}>
                                            <td className='text-center'>{new Date(waterIngestion.created_at).toLocaleTimeString('pt-BR')}</td>
                                            <td className='text-center'>{waterIngestion.amount} ml</td>
                                            <td className='text-center'>
                                                <Button variant="danger" onClick={() => handleDeleteButtonClick(waterIngestion.id)}><FiTrash /></Button>
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