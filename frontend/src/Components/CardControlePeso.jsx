import React, { useState } from 'react'
import useUserProfileData from '../App/useUserProfileData'
import useWeightControlData from '../App/useWeightControlData'
import RegisterWeightControlModal from './RegisterWeightControlModal';
import { confirm } from "./ConfirmationModal";

import Card from 'react-bootstrap/Card';
import { FiTrash } from 'react-icons/fi';
import Button from 'react-bootstrap/esm/Button';
import Spinner from 'react-bootstrap/Spinner';
import { useEffect } from 'react';

const CardControlePeso = () => {
    const { weightControlData, setWeightControlData, deleteWeightControl } = useWeightControlData(5)
    const { userProfileData, getUserProfileData } = useUserProfileData()
    const [weight, setWeight] = useState(0);
    const [userWeight, setUserWeight] = useState(getUserProfileData().weight);

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
        setUserWeight(getUserProfileData().weight)
    }
    , [userWeight,getUserProfileData])

    return (
        <>
            <Card className="mb-3">
                <Card.Header className='d-flex'>
                    <Card.Title>Controle de Peso</Card.Title>
                    <RegisterWeightControlModal handleRegisterWeightControl={handleRegisterWeightControl} setWeight={setWeight} />
                </Card.Header>
                {(weightControlData && Array.isArray(weightControlData)) ? (
                    <Card.Body>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Peso atual:</strong> {userWeight}  Kg</Card.Subtitle>                        
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
                                {weightControlData.map((weightControl, index) => {
                                    return (
                                        <tr key={index}>
                                            <td className='text-center'>{new Date(weightControl.created_at).toLocaleDateString('pt-BR')}</td>
                                            <td className='text-center'>{weightControl.weight} Kg</td>
                                            <td className='text-center'>
                                                <Button variant='danger' title={'Remover registro'} onClick={(e) => handleDeleteButtonClick(weightControl.id)}><FiTrash /></Button>
                                            </td>
                                        </tr>
                                    );
                                })}
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