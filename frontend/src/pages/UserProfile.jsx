import React, { useEffect } from 'react'

import { useAuth } from "../hooks/auth";

import CardProfilePhoto from '../components/CardProfilePhoto'
import CardConsumoAguaHoje from '../components/CardConsumoAguaHoje'
import CardControlePeso from '../components/CardControlePeso'

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';


const UserProfile = () => {
    const { cookies, checkToken, refreshUser } = useAuth();

    useEffect(() => {
        const token = cookies.token
        const keepLoggedIn = cookies.keepLoggedIn
        if (checkToken(token) === false && keepLoggedIn === true) {
            refreshUser();
        }
    }
    , [cookies.token])

    return (
        <>
            <div className="app-container">
                <div className="content">
                    <Container>
                        <Row>
                            <Col lg={4}>
                                <CardProfilePhoto />
                            </Col>
                            <Col lg={8}>
                                <Row>
                                    <Col md={6}>
                                        <CardConsumoAguaHoje />
                                    </Col>
                                    <Col md={6}>
                                        <CardControlePeso />
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