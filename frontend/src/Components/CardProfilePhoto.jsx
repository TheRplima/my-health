import React from 'react'
import useUserProfileData from '../App/useUserProfileData'
import Utils from '../App/utils'
import Card from 'react-bootstrap/Card';
import ListGroup from 'react-bootstrap/ListGroup';
import Spinner from 'react-bootstrap/Spinner';

const CardProfilePhoto = () => {
    const { formatPhoneNumber, getActivityLevel } = Utils()
    const { getUserProfileData } = useUserProfileData()

    const userProfileData = getUserProfileData()

    return (
        <>
            {(userProfileData) ? (
                <Card className="mb-3">
                    <Card.Img className="mx-auto rounded-circle" variant="top" src={userProfileData.gender === 'M' ? 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp' : 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava2.webp'} />
                    <Card.Body>
                        <ListGroup variant="flush">
                            <ListGroup.Item>
                                <strong>Nome:</strong> <small>{userProfileData.name}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Email:</strong> <small>{userProfileData.email}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Telefone:</strong> <small>{formatPhoneNumber(userProfileData.phone)}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Data Nascimento:</strong> <small>{new Date(userProfileData.dob).toLocaleDateString('pt-BR')}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Idade:</strong> <small>{new Date().getFullYear() - new Date(userProfileData.dob).getFullYear()} anos</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Sexo:</strong> <small>{userProfileData.gender === 'M' ? 'Masculino' : (userProfileData.gender === 'F' ? 'Feminino' : '')}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Nível de Atividade:</strong> <small>{getActivityLevel(parseFloat(userProfileData.activity_level))} </small>
                            </ListGroup.Item>
                        </ListGroup>
                    </Card.Body>
                </Card>
            ) : (
                <Card className="mb-3">
                    <Card.Body className='text-center'>
                        <Spinner animation="border" role="status">
                            <span className="sr-only">Loading...</span>
                        </Spinner>
                    </Card.Body>
                </Card>
            )}
        </>
    )
}

export default CardProfilePhoto