import { useState } from 'react'
import { jwtDecode } from "jwt-decode";

const useToken = () => {
  const getToken = () => {
    const tokenString = sessionStorage.getItem('token')
    const userToken = JSON.parse(tokenString)
    
    if (userToken === null) {
      return null;
    }
    let decodedToken = jwtDecode(userToken.token);
    let currentDate = new Date();
  
    // JWT exp is in seconds
    if (decodedToken.exp * 1000 < currentDate.getTime()) {
      return null;
    }

    return userToken?.token
  }

  const [token, setToken] = useState(getToken())

  const saveToken = userToken => {

    if (userToken !== null && userToken !== undefined){
      if (userToken.authorisation === null || userToken.authorisation === undefined){
        userToken.authorisation = {token: userToken, type: "bearer"}
      }
      sessionStorage.setItem('token', JSON.stringify(userToken.authorisation))
      setToken(userToken.authorisation.token)
    }else{
      sessionStorage.removeItem('token')
      setToken(null)
    }
  }
  return {
    getToken: getToken,
    setToken: saveToken,
    token
  }
}

export default useToken