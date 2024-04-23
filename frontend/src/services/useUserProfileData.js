import { useState } from 'react'
import { useCookies } from 'react-cookie';
import api from './api';
import { useNavigate } from 'react-router-dom';

const apiPrivate = (token) => {
    api.defaults.headers.Authorization = `Bearer ${token}`;
    return api
}

async function getUserProfile(token) {
    apiPrivate(token);

    return api.get('api/user').then(response => {
        return response.data
    }).catch(error => {
        console.log('Error', error.message);
    });
}

async function registerUserProfile(userData, token) {
    apiPrivate(token);
    const { name, email, password, password_confirmation } = userData

    return api.post('api/user', { name, email, password, password_confirmation }).then(response => {
        return response.data
    }).catch(error => {
        console.log('Error', error.message);
    });
}

async function updateUserProfile(id, userData, token) {
    apiPrivate(token);
    const { name, email, phone, dob, gender, activity_level, image } = userData
    if (image) {
        api.defaults.headers.ContentType = 'multipart/form-data';
    }
    return api.put('api/user/' + id, { name, email, phone, dob, gender, activity_level, image }).then(response => {
        return response.data
    }).catch(error => {
        console.log('Error', error.message);
    });
}

async function deleteUserProfile(id, token) {
    apiPrivate(token);

    return api.delete(`api/user/${id}`).then(response => {
        return response.data
    }).catch(error => {
        console.log('Error', error.message);
    });
}

const useUserProfileData = (init = true) => {
    const navigate = useNavigate();
    const [cookies, setCookies] = useCookies();

    const handleGetUserProfile = async (refresh = false) => {

        if (cookies.user && refresh === false) {
            return cookies.user
        }

        const token = cookies.token

        getUserProfile(token).then(data => {
            setCookies('user', JSON.stringify(data.data));

            return data.data
        }).catch((error) => {
            console.log('Error', error.message);
        });

        return {}

    }

    const [userProfileData] = useState(init ? handleGetUserProfile() : {})

    const handleRegisterUserProfile = async (userData) => {
        const token = cookies.token

        registerUserProfile(userData, token).then(response => {
            console.log(response)
            setCookies('token', response.authorisation.token);
            setCookies('user', JSON.stringify(response.user));
            alert('Cadastro realizado com sucesso!')
            navigate(process.env.REACT_APP_HOME_PAGE);
        }).catch((error) => {
            console.log('Error', error.message);
        });
    }

    const handleDeleteUserProfile = async (id) => {
        const token = cookies.token

        deleteUserProfile(id, token).then(data => data).catch((error) => {
            console.log('Error', error.message);
        });
    }

    const handleUpdateUserProfile = async (id, userData) => {
        const token = cookies.token

        updateUserProfile(id, userData, token).then((data) => {
            setCookies('user', JSON.stringify(data.user));

            return data.user
        }).catch((error) => {
            console.log('Error', error.message);
        });
    }

    return {
        getUserProfileData: handleGetUserProfile,
        setUserProfileData: handleRegisterUserProfile,
        deleteUserProfile: handleDeleteUserProfile,
        updateUserProfile: handleUpdateUserProfile,
        userProfileData
    }
}

export default useUserProfileData