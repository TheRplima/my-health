import React, { useState } from 'react'
import useUserProfileData from '../App/useUserProfileData'
import useWeightControlData from '../App/useWeightControlData'
import RegisterWeightControlModal from './RegisterWeightControlModal';

import Card from 'react-bootstrap/Card';
import { FiTrash } from 'react-icons/fi';
import Button from 'react-bootstrap/esm/Button';
import Spinner from 'react-bootstrap/Spinner';

async function RegisterWeightControl(weight, token) {
    return fetch('http://localhost:8000/api/water-ingestion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token,
        },
        body: JSON.stringify({ weight })
    }).then(data => data.json()).catch((error) => {
        console.log('Error', error.message);
    });
}

const CardControlePeso = (token) => {
    const [weight, setWeight] = useState(0);
    const { userProfileData } = useUserProfileData()
    const { weightControlData, setWeightControlData } = useWeightControlData()

    const handleRegisterWeightControl = async (e) => {
        const ret = await RegisterWeightControl(weight, token.token)
        setWeightControlData(ret.weight_control)
    }

    return (
        <>
            <Card className="mb-3">
                <Card.Header className='d-flex'>
                    <Card.Title>Controle de Peso</Card.Title>
                    <RegisterWeightControlModal handleRegisterWeightControl={handleRegisterWeightControl} setWeight={setWeight} />
                </Card.Header>
                {(weightControlData && Array.isArray(weightControlData)) ? (
                    <Card.Body>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Peso atual:</strong> {userProfileData.weight} Kg</Card.Subtitle>
                        <Card.Subtitle className="mb-3 text-muted"><strong>Progressão nos últimos 10 dias</strong></Card.Subtitle>
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
                                            <td className='text-center'>{new Date(weightControl.created_at).toLocaleTimeString('pt-BR')}</td>
                                            <td className='text-center'>{weightControl.weight} Kg</td>
                                            <td className='text-center'>
                                                <Button variant='danger' title={'Remover registro'}><FiTrash /></Button>
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