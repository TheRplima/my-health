import React from 'react'

import useToken from './App/useToken'
import useUserProfileData from './App/useUserProfileData'

import UserProfile from './Components/UserProfile'
import Homepage from './Components/Homepage'
import Register from './Components/Register'
import {BrowserRouter, Routes,Route } from "react-router-dom"
import 'bootstrap/dist/css/bootstrap.min.css';

const App = () => {
  const { setToken } = useToken()
  const { setUserProfileData } = useUserProfileData()

  return (
    <div className="App">
      <BrowserRouter> 
      <Routes>
        <Route path="/" element={<Homepage/>} />
        <Route path="/register" element={<Register setToken={setToken} setUserProfileData={setUserProfileData} />} />
        <Route path="/UserProfile" element={<UserProfile/>} />
      </Routes>
      </BrowserRouter>
    </div>
  )
}

export default App