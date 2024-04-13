import React from 'react'
import useToken from '../App/useToken'
import useUserProfileData from '../App/useUserProfileData'
import Header from './Header'
import Login from '../Login/Login'
import CardConsumoAguaHoje from './CardConsumoAguaHoje'

import Card from 'react-bootstrap/Card';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Button from 'react-bootstrap/Button';
import ListGroup from 'react-bootstrap/ListGroup';
import CardControlePeso from './CardControlePeso'

function formatPhoneNumber(input) {
    if (!input) return input;
    const numberInput = input.replace(/[^\d]/g, "");
    const numberInputLength = numberInput.length;

    if (numberInputLength < 4) {
        return numberInput;
    } else if (numberInputLength < 7) {
        return `(${numberInput.slice(0, 3)}) ${numberInput.slice(3)}`;
    } else if (numberInputLength < 11) {
        return `(${numberInput.slice(0, 2)}) ${numberInput.slice(2, 6)}-${numberInput.slice(6, 10)}`;
    } else {
        return `(${numberInput.slice(0, 2)}) ${numberInput.slice(2, 7)}-${numberInput.slice(7, 11)}`;
    }
}

const UserProfile = () => {
    const { token, setToken } = useToken()
    const { userProfileData, setUserProfileData } = useUserProfileData()

    if (!token) return (<Login setToken={setToken} setUserProfileData={setUserProfileData} />);

    return (
        <>
            <Header />
            <div className="app-container">
                <div className="content">
                    <Container>
                        <Row>
                            <Col md={4}>
                                <Card className="mb-3">
                                    <Card.Img className="mx-auto rounded-circle" variant="top" src={userProfileData.gender === 'M' ? 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp' : 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava2.webp'} />
                                    <Card.Body>
                                        <Card.Title>{userProfileData.name}</Card.Title>
                                        <Card.Text>
                                            {/* Calcular a idade em anos do usuario com base na data de nascimento */}
                                            {new Date().getFullYear() - new Date(userProfileData.dob).getFullYear()} anos
                                            <br />
                                            {userProfileData.weight} kg
                                        </Card.Text>
                                        <Button variant="primary">Go somewhere</Button>
                                    </Card.Body>
                                </Card>
                            </Col>
                            <Col md={8}>
                                <Row>
                                    <Col>
                                        <Card className="mb-3">
                                            <Card.Body>
                                                <ListGroup variant="flush">
                                                    <ListGroup.Item>
                                                        <strong>Nome:</strong> {userProfileData.name}
                                                    </ListGroup.Item>
                                                    <ListGroup.Item>
                                                        <strong>Email:</strong> {userProfileData.email}
                                                    </ListGroup.Item>
                                                    <ListGroup.Item>
                                                        <strong>Telefone:</strong> {formatPhoneNumber(userProfileData.phone)}
                                                    </ListGroup.Item>
                                                    <ListGroup.Item>
                                                        <strong>Data Nascimento:</strong> {new Date(userProfileData.dob).toLocaleDateString('pt-BR')}
                                                    </ListGroup.Item>
                                                    <ListGroup.Item>
                                                        <strong>Sexo:</strong> {userProfileData.gender === 'M' ? 'Masculino' : (userProfileData.gender === 'F' ? 'Feminino' : '')}
                                                    </ListGroup.Item>
                                                </ListGroup>
                                            </Card.Body>
                                        </Card>
                                    </Col>
                                </Row>
                                <Row>
                                    <Col md={6}>
                                        <CardConsumoAguaHoje token={token} />
                                    </Col>
                                    <Col md={6}>
                                        <CardControlePeso token={token} />
                                    </Col>
                                </Row>
                            </Col>
                        </Row>
                    </Container>    
                </div>
            </div>
        </>
    )
}

export default UserProfile