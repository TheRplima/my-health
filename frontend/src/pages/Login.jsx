import React, { useEffect, useState } from 'react';
import { useAuth } from "../hooks/auth";

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Form from 'react-bootstrap/Form';
import Button from 'react-bootstrap/Button';
import { Link } from 'react-router-dom';
import { FiLogIn } from 'react-icons/fi';
import { useNavigate } from 'react-router-dom';

export default function Login() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [keepLoggedIn, setKeepLoggedIn] = useState('');
    const { login, cookies } = useAuth();
    const navigate = useNavigate();

    const handleSubmit = (event) => {
        event.preventDefault();
        login({ email, password, keepLoggedIn })
    };

    useEffect(() => {
        const token = cookies.token
        const keepLoggedIn = cookies.keepLoggedIn
        if (token && keepLoggedIn) {
            navigate(process.env.REACT_APP_HOME_PAGE);
        }
        if (keepLoggedIn) {
            setKeepLoggedIn(true)
        }
    }, []);

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
                            <Form onSubmit={handleSubmit}>
                                <Form.Group className="mb-3" controlId="loginFormEmail">
                                    <Form.Label>Email</Form.Label>
                                    <Form.Control required type="email" onChange={(e) => setEmail(e.target.value)} />
                                </Form.Group>

                                <Form.Group className="mb-3" controlId="loginFormPassword">
                                    <Form.Label>Senha</Form.Label>
                                    <Form.Control required type="password" minLength={6} onChange={(e) => setPassword(e.target.value)} />
                                </Form.Group>

                                <Form.Group className="mb-3" controlId="keepLogggedIn">
                                    <Form.Check // prettier-ignore
                                        type='checkbox'
                                        id={'keepLoggedIn'}
                                        label={'Manter conectado'}
                                        onChange={(e) => setKeepLoggedIn(e.target.checked)}
                                        checked={keepLoggedIn}
                                    />
                                </Form.Group>

                                <Button variant="primary" type="submit">
                                    Enviar
                                </Button>
                            </Form>
                        </Col>
                    </Row>
                </Container>
            </div>
        </div>
    )
}