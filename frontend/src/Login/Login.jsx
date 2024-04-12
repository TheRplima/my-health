import React, { useState } from 'react'
import { Link } from 'react-router-dom';
import { FiLogIn } from 'react-icons/fi';
import PropTypes from 'prop-types'

async function LoginUser(credentials) {
    return fetch('http://localhost:8000/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(credentials)
    }).then(data => data.json()).catch((error) => {
        console.log('Error', error.message);
    });
}

const Login = (props) => {
    const [email, setEmail] = useState()
    const [password, setPassword] = useState()

    const handleSubmit = async (e) => {
        e.preventDefault();
        const ret = await LoginUser({
            email,
            password
        })
        props.setToken(ret)
        props.setUserProfileData(ret)
    }

    return (
        <div className="app-container">
            <div className="content">
                <section>
                    <h1>Login</h1>
                    <p>Preencha seus dados e faça login no sistema para ter acesso a todas as funcionalidades.</p>

                    <Link className="back-link" to="/register">
                        <FiLogIn size={16} color="#3498db" />
                        Não tenho cadastro
                    </Link>
                </section>
                <section className="form">
                    <form onSubmit={handleSubmit}>
                        <input
                            placeholder="Seu e-mail"
                            value={email}
                            required
                            onChange={(e) => setEmail(e.target.value)}
                        />
                        <input
                            placeholder="Sua Senha"
                            type="password"
                            value={password}
                            required
                            onChange={(e) => setPassword(e.target.value)}
                        />

                        <button className="button" type="submit">Entrar</button>
                    </form>
                </section>
            </div>
        </div>
    )
}

Login.propTypes = {
    setToken: PropTypes.func.isRequired,
    setUserProfileData: PropTypes.func.isRequired
}

export default Login