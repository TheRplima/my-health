import React from 'react'
import { useAuth } from "../hooks/auth";

import Container from 'react-bootstrap/Container';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import NavDropdown from 'react-bootstrap/NavDropdown';
import Button from 'react-bootstrap/Button';

const Header = () => {

    const { logout, cookies } = useAuth();

    const handleSubmit = async (e) => {
        e.preventDefault();
        logout();
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
                    {cookies.token ? (<Button variant="primary" onClick={handleSubmit}>Logout</Button>) : null}
                </Container>
            </Navbar>
        </header>
    )
}

export default Header