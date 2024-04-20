import React, { useState, useEffect } from 'react'

import { useAuth } from "../hooks/auth";

import useWeightControlData from '../services/useWeightControlData'
import RegisterWeightControlModal from './RegisterWeightControlModal';
import { confirm } from "./ConfirmationModal";

import Card from 'react-bootstrap/Card';
import { FiTrash } from 'react-icons/fi';
import Button from 'react-bootstrap/esm/Button';
import Spinner from 'react-bootstrap/Spinner';

const CardControlePeso = () => {
    const [weight, setWeight] = useState(0);
    const [weightControls, setWeightControls] = useState(null);
    const [loading, setLoading] = useState(true);
    const { cookies } = useAuth();
    const { setWeightControlData, deleteWeightControl } = useWeightControlData(5)

    const handleRegisterWeightControl = async (e) => {
        setWeightControlData(weight)
    }

    const handleDeleteButtonClick = (id) => {
        confirm('Deseja realmente excluir este registro?', 'Remover registro', 'Sim', 'Não').then(
            (response) => {
                if (response) {
                    deleteWeightControl(id)
                }
            })
    }

    useEffect(() => {
        async function loadStorageData() {
            const storageWeightControl = cookies.weight_controls;

            if (storageWeightControl) {
                setWeightControls(storageWeightControl);
                setLoading(false);
            }
        }

        loadStorageData();

        return () => {
            setWeightControls(null);
            setLoading(true);
        }
    }, [cookies.weight_controls]);

    return (
        <>
            <Card className="mb-3">
                <Card.Header className='d-flex'>
                    <Card.Title>Controle de Peso</Card.Title>
                    <RegisterWeightControlModal handleRegisterWeightControl={handleRegisterWeightControl} setWeight={setWeight} />
                </Card.Header>
                {(!loading) ? (
                    <Card.Body>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Peso atual:</strong> {weightControls[weightControls.length-1].weight}  Kg</Card.Subtitle>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Últimos 5 registros</strong></Card.Subtitle>
                        <table className="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" className='text-center'>Data</th>
                                    <th scope="col" className='text-center'>Peso</th>
                                    <th scope="col" className='text-center'>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {weightControls.length > 0 ? (
                                    weightControls.map((weightControl, index) => (
                                        <tr key={index}>
                                            <td className='text-center'>{new Date(weightControl.created_at).toLocaleDateString('pt-BR')}</td>
                                            <td className='text-center'>{weightControl.weight} Kg</td>
                                            <td className='text-center'>
                                                <Button variant='danger' title={'Remover registro'} onClick={(e) => handleDeleteButtonClick(weightControl.id)}><FiTrash /></Button>
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

export default CardControlePeso