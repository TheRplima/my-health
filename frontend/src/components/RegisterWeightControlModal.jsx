import React, { useState } from 'react';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import { FiPlusCircle } from 'react-icons/fi';

export default function RegisterWeightControl(props) {
    const [show, setShow] = useState(false);
    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);
    const handleAdd = () => {
        props.handleRegisterWeightControl();
        handleClose();
    }
    return (
        <>
            <Button className='ms-auto' size='sm' variant="primary" title='Registrar peso atual' onClick={handleShow} >
                <FiPlusCircle className="me-1" />
            </Button>

            <Modal show={show} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Registrar peso atual</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <input type="number" name="weight" placeholder="Peso atual em Kg" onChange={e => props.setWeight(e.target.value)} autoFocus={true} style={{'width':'100%'}} />
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