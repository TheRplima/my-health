import { useState } from 'react'
import useToken from './useToken';

async function refreshUserData(token) {
  return fetch('http://localhost:8000/api/refresh', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + token,
    }
  }).then(data => data.json()).catch((error) => {
    console.log('Error', error.message);
  });
}

const useUserProfileData = () => {
  const { getToken, setToken } = useToken()

  const getUserProfileData = () => {
    const userProfileDataString = sessionStorage.getItem('userprofiledata')
    const userProfileData = JSON.parse(userProfileDataString)
    return userProfileData
  }

  const handleRefreshUserData = async (e) => {
    const token = getToken()

    if (token === null || token === undefined) {
      alert('Sessão expirada. Faça login novamente.')
      sessionStorage.clear();
      window.location.reload();
    }

    refreshUserData(token).then(data => {
    
      setToken(data)
      saveUserProfileData(data);

      return data
    }).catch((error) => {
      console.log('Error', error.message);
    });

  }

  const [userProfileData, setUserProfileData] = useState(getUserProfileData())

  const saveUserProfileData = (userProfileData) => {
    if (userProfileData !== null && userProfileData !== undefined) {
      sessionStorage.setItem('userprofiledata', JSON.stringify(userProfileData.user || userProfileData))
      setUserProfileData(userProfileData.user)
    }
  }

  return {
    getUserProfileData: getUserProfileData,
    setUserProfileData: saveUserProfileData,
    refreshUserData: handleRefreshUserData,
    userProfileData
  }
}

export default useUserProfileData