import React from 'react';
import { Route, Routes, Navigate } from 'react-router-dom';
import { ProtectRoutes } from './hooks/protectRoutes';
import Header from './components/Header';
import Login from './pages/Login';
import Register from './pages/Register';
import Home from './pages/Home';
import UserProfile from './pages/UserProfile';

export default function App() {
  return (
    <>
      <Header />
      <Routes>
        <Route path='/' element={<Navigate to='home' exact />} />
        <Route path='/home' element={<Home />} />
        <Route path='/login' element={<Login />} />
        <Route path='/register' element={<Register />} />

        <Route element={<ProtectRoutes />}>
          <Route path='/profile' element={<UserProfile />} />
        </Route>
      </Routes>
    </>
  )
}