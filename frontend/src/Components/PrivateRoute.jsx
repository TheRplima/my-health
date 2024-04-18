import React, { useState, useEffect } from 'react'
import { Outlet } from 'react-router-dom'
import Login from './Login'

const PrivateRoute = ({ component: Component, ...rest }) => {
    const [isAuthenticated, setIsAuthenticated] = useState(false)

    useEffect(() => {
        const token = sessionStorage.getItem('token')
        if (token) {
            setIsAuthenticated(true)
        }
    }, [])

    return isAuthenticated ? <Outlet/> : <Login />
}
export default PrivateRoute;