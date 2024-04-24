import React, { useState } from 'react';
import Form from 'react-bootstrap/Form';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import { Dropdown } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';

import useWaterIntakeContainerData from '../services/useWaterIntakeContainerData';
import { useAuth } from "../hooks/auth";

export default function RegisterWaterIntakeContainer() {
    const [show, setShow] = useState(false);
    const [name, setName] = useState('');
    const [size, setSize] = useState('');
    const [icon, setIcon] = useState('');

    const { setWaterIntakeContainerData } = useWaterIntakeContainerData();
    const { cookies } = useAuth();

    library.add(fas);

    const allowedContainers = [
        'glass-water',
        'bottle-water',
        'wine-glass',
        'whiskey-glass',
        'martini-glass',
        'mug-hot',
        'beer-mug-empty',
        'wine-bottle',
        'bottle-droplet'
    ]

    const handleClose = () => {
        setIcon('');
        setShow(false);
    }
    const handleShow = () => setShow(true);

    const handleAdd = () => {
        const newContainer = {
            user_id: cookies.user.id,
            name: name,
            size: size,
            icon: icon,
            active: 1
        }
        setWaterIntakeContainerData(newContainer);
        setIcon('');
        handleClose();
    }

    return (
        <>
            <Button variant="success" title={'Cadastrar novo recipiente'} onClick={handleShow}>
                <FontAwesomeIcon icon={['fa', 'plus']} />
            </Button>

            <Modal show={show} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Novo recipiente de água</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form>
                        <Form.Group className="mb-3" controlId="registerFormName">
                            <Form.Label>Descrição</Form.Label>
                            <Form.Control required type="text" placeholder="Nome para o recipiente" onChange={(e) => setName(e.target.value)} />
                        </Form.Group>

                        <Form.Group className="mb-3" controlId="registerFormEmail">
                            <Form.Label>Tamanho</Form.Label>
                            <Form.Control required type="number" placeholder="Capacidade em ml do recipiente" onChange={(e) => setSize(e.target.value)} />
                        </Form.Group>

                        <Form.Group className="mb-3" controlId="registerFormPassword">
                            <Form.Label>Ícone</Form.Label>
                            <Dropdown onSelect={(evtKey) => setIcon(evtKey)} className="d-grid gap-2">
                                <Dropdown.Toggle variant={'outline-secondary'} bg={'light'}>
                                    Ícone para o recipiente {' '}
                                    {icon !== '' && <FontAwesomeIcon icon={['fas', icon]} size='lg' />}
                                </Dropdown.Toggle>
                                <Dropdown.Menu>
                                    {allowedContainers.map((container) => {
                                        return (
                                            <Dropdown.Item key={container} eventKey={container}>
                                                <FontAwesomeIcon icon={['fas', container]} size='lg' />
                                            </Dropdown.Item>
                                        );
                                    })}                                    
                                </Dropdown.Menu>
                            </Dropdown>
                        </Form.Group>
                    </Form>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="danger" onClick={handleClose}>
                        Cancelar
                    </Button>
                    <Button variant="primary" onClick={handleAdd}>
                        Registrar
                    </Button>
                </Modal.Footer>
            </Modal>
        </>
    );
}