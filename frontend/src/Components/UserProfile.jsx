import React from 'react'
import useToken from '../App/useToken'
import useUserProfileData from '../App/useUserProfileData'
import Header from './Header'
import Login from '../Login/Login'
import CardConsumoAguaHoje from './CardConsumoAguaHoje'

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import CardControlePeso from './CardControlePeso'
import CardProfilePhoto from './CardProfilePhoto'

const UserProfile = () => {
    const { token, setToken } = useToken()
    const { setUserProfileData } = useUserProfileData()

    if (!token) return (<Login setToken={setToken} setUserProfileData={setUserProfileData} />);

    return (
        <>
            <Header />
            <div className="app-container">
                <div className="content">
                    <Container>
                        <Row>
                            <Col md={4}>
                                <CardProfilePhoto />
                            </Col>
                            <Col md={8}>
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