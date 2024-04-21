import React, { useState } from 'react';
import Form from 'react-bootstrap/Form';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import { FiPlusCircle } from 'react-icons/fi';

export default function RegisterWaterIntake(props) {
    const [show, setShow] = useState(false);
    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);
    const handleAdd = () => {
        props.handleRegisterWaterIntake();
        handleClose();
    }
    return (
        <>
            <Button className='ms-auto' size='sm' variant="primary" title='Registrar consumo de água' onClick={handleShow} >
                <FiPlusCircle className="me-1" />
            </Button>

            <Modal show={show} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Registrar consumo de água</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form.Control required type="number" name="amount" placeholder="Quantidade de água ingerida em ml" onChange={e => props.setAmount(e.target.value)} autoFocus={true} />
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