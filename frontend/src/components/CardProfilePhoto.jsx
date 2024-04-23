import React, { useEffect, useState } from 'react'

import useUserProfileData from "../services/useUserProfileData";
import { useAuth } from "../hooks/auth";
import Utils from '../hooks/utils'

import Card from 'react-bootstrap/Card';
import ListGroup from 'react-bootstrap/ListGroup';
import Spinner from 'react-bootstrap/Spinner';
import Button from 'react-bootstrap/Button';

import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { Col, Form, Row } from 'react-bootstrap';

const CardProfilePhoto = () => {
    const { formatPhoneNumber, getActivityLevel } = Utils()
    const { cookies } = useAuth();
    const { updateUserProfile, getUserProfileData } = useUserProfileData();

    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [editing, setEditing] = useState(false);
    const [file, setFile] = useState()

    library.add(fas);

    const handleEditProfile = () => {
        setEditing(true)
    }

    const handleEditProfileCancelBtn = () => {
        setEditing(false)
    }

    const handleChange = async (event) => {
        const base64 = await convertBase64(event.target.files[0])
        setFile(base64)
      }

      const convertBase64 = (file) => {
        return new Promise((resolve, reject) => {
          const fileReader = new FileReader();
          fileReader.readAsDataURL(file)
          fileReader.onload = () => {
            resolve(fileReader.result);
          }
          fileReader.onerror = (error) => {
            reject(error);
          }
        })
      }

    const handleEditProfileFormSubmit = (e) => {
        e.preventDefault()

        const formData = new FormData(e.target),
            formDataObj = Object.fromEntries(formData.entries())
        formDataObj.phone = formDataObj.phone.replace(/\D/g, '');
        formDataObj.image = formDataObj.image ? file : null;
        updateUserProfile(user.id, formDataObj);

        setLoading(false);
        setEditing(false)
    }

    useEffect(() => {
        async function loadStorageData() {
            const storageUser = cookies.user;
            const storageToken = cookies.token;

            if (storageUser && storageToken) {
                setUser(storageUser)
            }
            setLoading(false);
        }

        loadStorageData();
    }, [cookies.user]);

    return (
        <>
            {(!loading && user) ? (
                <Card className="mb-3" id={'profile-card'}>
                    <Card.Header className={editing ? 'editing' : ''}>
                        {editing ? (<Card.Title>Editar Perfil</Card.Title>) : <Card.Title>Perfil</Card.Title>}
                        <Button variant="primary" size={'sm'} title={'Editar Perfil'} onClick={handleEditProfile}>
                            <FontAwesomeIcon icon={['fa', 'pen']} />
                        </Button>
                    </Card.Header>
                    {!editing ? (
                        <Card.Img className="m-auto p-5 rounded-circle" variant="top" src={
                            user.image ? (
                                process.env.REACT_APP_API_BASE_URL + 'storage/' + user.image
                            ) : (
                                user.gender === 'M' ? 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp' : 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava2.webp'
                            )
                        } />
                    ) : ('')}
                    <Card.Body>
                        {!editing ? (
                            <ListGroup variant="flush">
                                <ListGroup.Item>
                                    <strong>Nome:</strong> <small>{user.name}</small>
                                </ListGroup.Item>
                                <ListGroup.Item>
                                    <strong>Email:</strong> <small>{user.email}</small>
                                </ListGroup.Item>
                                {user.phone ? (
                                    <ListGroup.Item>
                                        <strong>Telefone:</strong> <small>{formatPhoneNumber(user.phone)}</small>
                                    </ListGroup.Item>
                                ) : ('')}
                                {user.dob ? (
                                    <>
                                        <ListGroup.Item>
                                            <strong>Data Nascimento:</strong> <small>{new Date(user.dob).toLocaleDateString('pt-BR')}</small>
                                        </ListGroup.Item>
                                        <ListGroup.Item>
                                            <strong>Idade:</strong> <small>{new Date().getFullYear() - new Date(user.dob).getFullYear()} anos</small>
                                        </ListGroup.Item>
                                    </>
                                ) : ('')}
                                {user.gender ? (
                                    <ListGroup.Item>
                                        <strong>Sexo:</strong> <small>{user.gender === 'M' ? 'Masculino' : (user.gender === 'F' ? 'Feminino' : '')}</small>
                                    </ListGroup.Item>
                                ) : ('')}
                                {user.activity_level ? (
                                    <ListGroup.Item>
                                        <strong>Nível de Atividade:</strong> <small>{getActivityLevel(parseFloat(user.activity_level))} </small>
                                    </ListGroup.Item>
                                ) : ('')}
                            </ListGroup>
                        ) : (
                            <Form onSubmit={handleEditProfileFormSubmit}>
                                <ListGroup variant="flush">
                                    <ListGroup.Item>
                                        <Form.Control type="file" name='image' onChange={handleChange} />
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        <strong>Nome:</strong> <Form.Control name="name" defaultValue={user.name} />
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        <strong>Email:</strong> <Form.Control required name="email" defaultValue={user.email} />
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        <strong>Telefone:</strong> <Form.Control name="phone" defaultValue={user.phone} />
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        <strong>Data Nascimento:</strong> <Form.Control type='date' name="dob" defaultValue={user.dob} />
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        <strong>Sexo:</strong> <Form.Select name='gender' defaultValue={user.gender}>
                                            <option value="M">Masculino</option>
                                            <option value="F">Feminino</option>
                                        </Form.Select>
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        <strong>Nível de Atividade:</strong> <Form.Select name='activity_level' defaultValue={user.activity_level}>
                                            <option value="0.2">No sport/exercise</option>
                                            <option value="0.375">Light activity (sport 1-3 times per week)</option>
                                            <option value="0.55">Moderate activity (sport 3-5 times per week)</option>
                                            <option value="0.725">Hard activity (sport 6-7 times per week)</option>
                                            <option value="0.9">Very hard activity (physical job, exercise multiple times per day)</option>
                                        </Form.Select>
                                    </ListGroup.Item>
                                </ListGroup>
                                <Row className='mt-3'>
                                    <Col className='d-flex justify-content-around'>
                                        <Button variant="danger" size={'sm'} title={'Cancelar'} onClick={handleEditProfileCancelBtn}>
                                            Cancelar
                                        </Button>
                                        <Button type='submit' variant="success" size={'sm'} title={'Editar'}>
                                            Editar
                                        </Button>
                                    </Col>
                                </Row>
                            </Form>
                        )}
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