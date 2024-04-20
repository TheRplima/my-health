import React, { useState } from 'react';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Form from 'react-bootstrap/Form';
import Button from 'react-bootstrap/Button';
import { Link } from 'react-router-dom';
import { FiArrowLeft } from 'react-icons/fi';
import { useAuth } from "../hooks/auth";

const Register = (props) => {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [validated, setValidated] = useState(false);

    const { register } = useAuth();

    const handleSubmit = (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const validPassword = password === confirmPassword;
        if (form.checkValidity() === false || validPassword !== true) {
            alert('Verifique os dados preenchidos. Todos os campos são obrigatórios')
            event.stopPropagation();
        }

        setValidated(true);
        register({ name, email, password, password_confirmation: confirmPassword })
    };

    return (
        <div className="login-container">
            <div className="content">
                <Container>
                    <Row>
                        <Col lg={6}>
                            <h1>Cadastro</h1>
                            <p>Faça seu cadastro, entre na plataforma e organize a suas finanças.</p>

                            <Link className="back-link mb-5" to="/login">
                                <FiArrowLeft size={16} color="#3498db" />
                                Já possuo cadastro
                            </Link>
                        </Col>
                        <Col lg={6}>
                            <Form noValidate validated={validated} onSubmit={handleSubmit}>
                                <Form.Group className="mb-3" controlId="registerFormName">
                                    <Form.Label>Nome</Form.Label>
                                    <Form.Control required type="text" placeholder="Digite seu nome completo" onChange={(e) => setName(e.target.value)} />
                                </Form.Group>

                                <Form.Group className="mb-3" controlId="registerFormEmail">
                                    <Form.Label>Email</Form.Label>
                                    <Form.Control required type="email" placeholder="Digite seu melhor email" onChange={(e) => setEmail(e.target.value)} />
                                    <Form.Text className="text-muted">
                                        Nunca compartilharemos seu email com ninguém.
                                    </Form.Text>
                                </Form.Group>

                                <Form.Group className="mb-3" controlId="registerFormPassword">
                                    <Form.Label>Senha</Form.Label>
                                    <Form.Control required type="password" placeholder="Senha" minLength={6} onChange={(e) => setPassword(e.target.value)} />
                                </Form.Group>

                                <Form.Group className="mb-3" controlId="registerFormPasswordConfirm">
                                    <Form.Label>Confirmação da Senha</Form.Label>
                                    <Form.Control required type="password" placeholder="Digite sua senha novamente" minLength={6} onChange={(e) => setConfirmPassword(e.target.value)} />
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
    );
}

export default Register