import React, {useEffect, useState} from 'react'

import { useAuth } from "../hooks/auth";
import Utils from '../hooks/utils'

import Card from 'react-bootstrap/Card';
import ListGroup from 'react-bootstrap/ListGroup';
import Spinner from 'react-bootstrap/Spinner';

const CardProfilePhoto = () => {
    const { formatPhoneNumber, getActivityLevel } = Utils()
    const { cookies } = useAuth();

    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
  
    useEffect(() => {
      async function loadStorageData() {
        const storageUser = cookies.user;
        const storageToken = cookies.token;
  
        if (storageUser && storageToken) {
          setUser(storageUser);
        }
        setLoading(false);
      }
  
      loadStorageData();
    }, []);

    return (
        <>
            {(!loading && user) ? (
                <Card className="mb-3">
                    <Card.Img className="mx-auto rounded-circle" variant="top" src={user.gender === 'M' ? 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp' : 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava2.webp'} />
                    <Card.Body>
                        <ListGroup variant="flush">
                            <ListGroup.Item>
                                <strong>Nome:</strong> <small>{user.name}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Email:</strong> <small>{user.email}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Telefone:</strong> <small>{formatPhoneNumber(user.phone)}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Data Nascimento:</strong> <small>{new Date(user.dob).toLocaleDateString('pt-BR')}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Idade:</strong> <small>{new Date().getFullYear() - new Date(user.dob).getFullYear()} anos</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Sexo:</strong> <small>{user.gender === 'M' ? 'Masculino' : (user.gender === 'F' ? 'Feminino' : '')}</small>
                            </ListGroup.Item>
                            <ListGroup.Item>
                                <strong>Nível de Atividade:</strong> <small>{getActivityLevel(parseFloat(user.activity_level))} </small>
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