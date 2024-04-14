import React from 'react'
import useToken from '../App/useToken'
import useUserProfileData from '../App/useUserProfileData';
import useWeightControlData from '../App/useWeightControlData';
import useWaterIngestionData from '../App/useWaterIngestionData';

import Container from 'react-bootstrap/Container';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import NavDropdown from 'react-bootstrap/NavDropdown';
import Button from 'react-bootstrap/Button';

const Header = () => {
    const { getToken, setToken } = useToken()
    const { setUserProfileData } = useUserProfileData()
    const { setWeightControlData } = useWeightControlData()
    const { setWaterIngestionData } = useWaterIngestionData()
    const token = getToken()

    const handleSubmit = async (e) => {
        e.preventDefault();
        sessionStorage.clear();
        setToken(null)
        setUserProfileData(null)
        setWeightControlData(null)
        setWaterIngestionData(null)
        window.location.reload();
    }

    return (
        <header className="App-header">
            <Navbar expand="lg" fixed="top" className="bg-body-tertiary">
                <Container>
                    <Navbar.Brand href="#home">My Health</Navbar.Brand>
                    <Navbar.Toggle aria-controls="basic-navbar-nav" />
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="me-auto">
                            <Nav.Link href="#home">Home</Nav.Link>
                            <Nav.Link href="#link">Link</Nav.Link>
                            <NavDropdown title="Dropdown" id="basic-nav-dropdown">
                                <NavDropdown.Item href="#action/3.1">Action</NavDropdown.Item>
                                <NavDropdown.Item href="#action/3.2">
                                    Another action
                                </NavDropdown.Item>
                                <NavDropdown.Item href="#action/3.3">Something</NavDropdown.Item>
                                <NavDropdown.Divider />
                                <NavDropdown.Item href="#action/3.4">
                                    Separated link
                                </NavDropdown.Item>
                            </NavDropdown>
                        </Nav>
                    </Navbar.Collapse>
                    {token ? (<Button variant="primary" onClick={handleSubmit}>Logout</Button>) : null}
                </Container>
            </Navbar>
        </header>
    )
}

export default Header