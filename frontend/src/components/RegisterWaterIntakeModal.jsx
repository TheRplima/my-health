import React, { useState } from 'react';
import Form from 'react-bootstrap/Form';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import FormControlWaterIntakeContainerSelect from './FormControlWaterIntakeContainerSelect';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';

export default function RegisterWaterIntake(props) {
    const [show, setShow] = useState(false);
    const [visibilityControl, setVisibilityControl] = useState(false);
    const [btnLabel, setBtnLabel] = useState(<FontAwesomeIcon icon={['fa', 'pen']} />);
    const [btnTitle, setBtnTitle] = useState('Inserir quantidade manualmente');

    library.add(fas);

    const handleClose = () => setShow(false);
    const handleShow = () => setShow(true);

    const handleAdd = () => {
        if (!props.amount || props.amount === 0 || props.amount === '0' || props.amount === '') {

            return alert('Informe a quantidade de água ingerida');
        }

        props.handleRegisterWaterIntake();
        handleClose();
    }

    const handleChangeVisibilityControl = () => {
        if (visibilityControl) {
            setVisibilityControl(false);
            setBtnLabel(<FontAwesomeIcon icon={['fa', 'pen']} />);
            setBtnTitle('Inserir quantidade manualmente');
        } else {
            setVisibilityControl(true);
            setBtnTitle('Escolher recipiente');
            setBtnLabel(<FontAwesomeIcon icon={['fa', 'left-long']} />);
        }
    }
    return (
        <>
            <Button className='ms-auto' size='sm' variant="primary" title='Registrar consumo de água' onClick={handleShow} >
                <FontAwesomeIcon icon={['fa', 'plus']} />
            </Button>

            <Modal show={show} onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Registrar consumo de água</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Row>
                        <Col md={10}>
                            {(!visibilityControl) ? (
                                <FormControlWaterIntakeContainerSelect setAmount={props.setAmount} />
                            ) : (
                                <Form.Control required type="number" name="amount" placeholder="Quantidade de água ingerida em ml" onChange={e => props.setAmount(e.target.value)} value={props.amount} autoFocus={true} />
                            )}
                        </Col>
                        <Col className='text-right'>
                            <Button variant="warning" size={'sm'} title={btnTitle} onClick={handleChangeVisibilityControl}>{btnLabel}</Button>
                        </Col>
                    </Row>
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