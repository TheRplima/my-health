import { useState } from 'react'
import { jwtDecode } from "jwt-decode";

const useToken = () => {
  const getToken = () => {
    const tokenString = sessionStorage.getItem('token')
    const currentDate = new Date();

    if (tokenString !== null && tokenString !== undefined) {
      const userToken = JSON.parse(tokenString)
      const decodedToken = jwtDecode(userToken.token);

      if (decodedToken.exp * 1000 > currentDate.getTime()) {
        return userToken.token
      }
    }

    return null
  }

  const [token, setToken] = useState(getToken())

  const saveToken = userToken => {

    if (userToken === null || userToken === undefined || userToken.message === 'Unauthenticated') {
      alert('Sessão expirada. Faça login novamente.')
      sessionStorage.clear();
      return;
    }

    if (typeof userToken === 'string') {
      userToken = { authorisation: { token: userToken, type: "bearer" } }
    }

    sessionStorage.setItem('token', JSON.stringify(userToken.authorisation))
    setToken(userToken.authorisation.token)
  }

  return {
    getToken: getToken,
    setToken: saveToken,
    token
  }
}

export default useToken