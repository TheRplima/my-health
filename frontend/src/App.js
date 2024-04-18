import React from 'react'
import { BrowserRouter, Routes, Route } from "react-router-dom"
import 'bootstrap/dist/css/bootstrap.min.css';

import UserProfile from './Components/UserProfile'
import Register from './Components/Register'
import PrivateRoute from './Components/PrivateRoute';

const App = () => {

  return (
    <div className="App">
      <BrowserRouter>
        <Routes>
          <Route path="/register" element={<Register />} />
          <Route element={<PrivateRoute />}>
            <Route path="/UserProfile" element={<UserProfile />} />
          </Route>
        </Routes>
      </BrowserRouter>
    </div>
  )
}

export default App