import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { FiArrowLeft } from 'react-icons/fi';
import PropTypes from 'prop-types'

async function RegisterUser(data) {
    return fetch('http://localhost:8000/api/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(data => data.json()).catch((error) => {
        console.log('Error', error.message);
    });
}

const Register = (props) => {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');

    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        const data = {
            name,
            email,
            password,
            "password_confirmation": confirmPassword,
        };
        const ret = await RegisterUser(data)
        alert('Cadastro realizado com sucesso!');
        props.setToken(ret)
        props.setUserProfileData(ret)
        navigate('/userProfile')
    }

    return (
        <div className="app-container">
            <div className="content">
                <section>
                    <h1>Cadastro</h1>
                    <p>Faça seu cadastro, entre na plataforma e organize a suas finanças.</p>

                    <Link className="back-link" to="/UserProfile">
                        <FiArrowLeft size={16} color="#3498db" />
                        Já possuo cadastro
                    </Link>
                </section>

                <form onSubmit={handleSubmit}>
                    <input
                        placeholder="Seu Nome"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                    />

                    <input
                        type="email"
                        placeholder="Seu E-mail"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                    />

                    <input
                        placeholder="Digite sua Senha"
                        value={password}
                        type="password"
                        onChange={(e) => setPassword(e.target.value)}
                    />

                    <input
                        placeholder="Confirme sua Senha"
                        value={confirmPassword}
                        type="password"
                        onChange={(e) => setConfirmPassword(e.target.value)}
                    />

                    <button className="button" type="submit">Cadastrar</button>
                </form>
            </div>
        </div>
    );
}

Register.propTypes = {
    setToken: PropTypes.func.isRequired,
    setUserProfileData: PropTypes.func.isRequired
}

export default Register