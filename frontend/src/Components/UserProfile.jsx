import React from 'react'
import Header from './Header'
import CardConsumoAguaHoje from './CardConsumoAguaHoje'

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import CardControlePeso from './CardControlePeso'
import CardProfilePhoto from './CardProfilePhoto'

const UserProfile = () => {

    return (
        <>
            <Header />
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