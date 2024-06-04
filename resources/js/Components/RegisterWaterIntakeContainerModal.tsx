import React, { useState } from 'react';
import { usePage } from '@inertiajs/react'
import Form from 'react-bootstrap/Form';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import { Dropdown } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { IconName } from '@fortawesome/fontawesome-common-types';
import { PageProps } from '@/types';
import { router } from '@inertiajs/react'

interface WaterIntakeContainer {
    id?: number;
    user_id?: number;
    name: string;
    size: number;
    icon: string;
    active: number;
}

export default function RegisterWaterIntakeContainer() {
    const { auth } = usePage<PageProps>().props;
    const [show, setShow] = useState(false);
    const [name, setName] = useState('');
    const [size, setSize] = useState<number>(0);
    const [icon, setIcon] = useState('');
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
        setName('');
        setSize(0);
        setShow(false);
    }
    const handleShow = () => setShow(true);

    const handleAdd = () => {
        if (!name || name === '' || !size || size === 0 || !icon || icon === '') {
            return alert('Preencha todos os campos');
        }
        const newContainer = {
            user_id: auth.user.id,
            name: name,
            size: size,
            icon: icon,
            active: 1
        } as WaterIntakeContainer;
        setWaterIntakeContainerData(newContainer);
        setIcon('');
        handleClose();
    }

    const setWaterIntakeContainerData = async (newContainer: any) => {
        const response = await router.post('/water-intake-container', newContainer);

        if (response !== undefined) {
            router.reload({ only: ['auth.waterIntakeContainers'] });
        }
    }

    return (
        <>
            <Button variant="success" title={'Cadastrar novo recipiente'} onClick={handleShow}>
                <FontAwesomeIcon icon={['fas', 'plus']} />
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
                            <Form.Control required type="number" placeholder="Capacidade em ml do recipiente" onChange={(e) => setSize(parseInt(e.target.value))} />
                        </Form.Group>

                        <Form.Group className="mb-3" controlId="registerFormPassword">
                            <Form.Label>Ícone</Form.Label>
                            <Dropdown onSelect={(evtKey: any) => setIcon(evtKey)} className="d-grid gap-2">
                                <Dropdown.Toggle variant={'outline-secondary'}>
                                    Ícone para o recipiente {' '}
                                    {icon !== '' && <FontAwesomeIcon icon={['fas', icon as IconName]} size='lg' />}
                                </Dropdown.Toggle>
                                <Dropdown.Menu>
                                    {allowedContainers.map((container) => {
                                        return (
                                            <Dropdown.Item key={container} eventKey={container}>
                                                <FontAwesomeIcon icon={['fas', container as IconName]} size='lg' />
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
