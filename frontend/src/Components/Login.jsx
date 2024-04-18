import React, { useState } from 'react'
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Link } from 'react-router-dom';
import { FiLogIn } from 'react-icons/fi';

import useToken from '../App/useToken'
import useUserProfileData from '../App/useUserProfileData';

async function LoginUser(credentials) {
    return fetch(process.env.REACT_APP_API_BASE_URL +'api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(credentials)
    }).then(data => data.json()).catch((error) => {
        console.log('Error', error.message);
    });
}

const Login = () => {
    const [email, setEmail] = useState()
    const [password, setPassword] = useState()
    const { setToken } = useToken()
    const { setUserProfileData } = useUserProfileData()
    
    const handleChangeEmail = (event) => {
        setEmail(event.target.value);
    };

    const handleChangePassword = (event) => {
        setPassword(event.target.value);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        LoginUser({
            email,
            password
        }).then(data => {
            setToken(data)
            setUserProfileData(data)
            window.location.reload();
        }).catch((error) => {
            console.log('Error', error.message);
        });
    }

    return (
        <div className="login-container">
            <div className="content">
                <Container>
                    <Row>
                        <Col lg={6}>
                            <h1>Login</h1>
                            <p>Preencha seus dados e faça login no sistema para ter acesso a todas as funcionalidades.</p>

                            <Link className="back-link mb-5" to="/register">
                                <FiLogIn size={20} color="#3498db" />
                                Não tenho cadastro
                            </Link>
                        </Col>
                        <Col lg={6}>
                            <form onSubmit={handleSubmit}>
                                <input
                                    placeholder="Seu e-mail"
                                    value={email !== undefined ? email : ''}
                                    required
                                    onChange={handleChangeEmail}
                                />
                                <input
                                    placeholder="Sua Senha"
                                    type="password"
                                    value={password !== undefined ? password : ''}
                                    required
                                    onChange={handleChangePassword}
                                />

                                <button className="button" type="submit">Entrar</button>
                            </form>
                        </Col>
                    </Row>
                </Container>
            </div>
        </div>
    )
}

export default Login