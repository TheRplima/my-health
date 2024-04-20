import React from 'react';

import { Link } from 'react-router-dom';

export default function Home() {
    return (
        <div className="app-container">
            <div className="content">
                <h1>Home</h1>
                <p>Seja bem-vindo(a) a home do sistema.</p>
                <Link className="back-link mb-5" to="/login">Fazer login</Link>
            </div>
        </div>
    )
}