import { createContext, useContext, useMemo } from 'react';
import { useCookies } from 'react-cookie';
import { useNavigate } from 'react-router-dom';
import api from '../../services/api';

const UserContext = createContext();

export const UserProvider = ({ children }) => {
    const navigate = useNavigate();
    const [cookies, setCookies, removeCookie] = useCookies();

    const apiPrivate = () => {
        const token = cookies?.token ?? null;
        api.defaults.headers.Authorization = `Bearer ${token}`;
        return api
    }

    const login = async ({ email, password }) => {
        api.post('api/login', {
            email: email,
            password: password
        }).then(response => {
            setCookies('token', response.data.authorisation.token);
            setCookies('user', JSON.stringify(response.data.user));

            navigate(process.env.REACT_APP_HOME_PAGE);
        }).catch(error => {
            console.log(error);
            alert('Erro ao realizar cadastro: ' + error.response.data.message);
        });
    };

    const logout = () => {
        ['token', 'user', 'water_ingestions', 'weight_controls'].forEach(obj => removeCookie(obj));
        navigate('/login');
    };

    const register = async (data) => {
        api.post('api/register', {
            name: data.name,
            email: data.email,
            password: data.password,
            password_confirmation: data.password_confirmation
        }).then(response => {
            setCookies('token', response.data.authorisation.token);
            setCookies('user', JSON.stringify(response.data.user));
            alert('Cadastro realizado com sucesso!')
            navigate(process.env.REACT_APP_HOME_PAGE);
        }).catch(error => {
            console.log(error);
            alert('Erro ao realizar cadastro: ' + error.response.data.message);
        });
    }

    const refreshUser = async () => {
        apiPrivate();

        return api.post('api/refresh').then(response => {
            setCookies('token', response.data.authorisation.token);
            setCookies('user', JSON.stringify(response.data.user));
        }).catch(error => {
            console.log('Error', error.message);
        });
    }

    const value = useMemo(
        () => ({
            cookies,
            login,
            logout,
            register,
            refreshUser
        }),
        [cookies]
    );

    return (
        <UserContext.Provider value={value}>
            {children}
        </UserContext.Provider>
    )
};

export const useAuth = () => {
    return useContext(UserContext)
};