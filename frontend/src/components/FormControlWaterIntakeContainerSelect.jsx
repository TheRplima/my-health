import React, { useState, useEffect } from 'react'
import { useAuth } from "../hooks/auth";
import useWaterIntakeContainerData from '../services/useWaterIntakeContainerData';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { Dropdown } from 'react-bootstrap';

function FormControlWaterIntakeContainerSelect(props) {
    const { waterIntakeContainerData } = useWaterIntakeContainerData();
    const { cookies } = useAuth();
    const [waterIntakeContainers, setWaterIntakeContainers] = useState(null);
    const [loading, setLoading] = useState(true);
    const [icon, setIcon] = useState('');
    library.add(fas);

    const handleOnSelect = (evtKey) => {
        const container = waterIntakeContainers.find(container => container.size === parseInt(evtKey));
        setIcon(container.icon);
        props.setAmount(evtKey);
    }

    useEffect(() => {
        async function loadStorageData() {
            const storageWaterIntakeContainers = cookies.water_intake_containers;
            if (storageWaterIntakeContainers) {
                setWaterIntakeContainers(storageWaterIntakeContainers);
                setLoading(false);
            }
        }

        loadStorageData();
        return () => {
            setWaterIntakeContainers(null);
            setLoading(true);
        }
    }, [cookies.water_intake_containers]);

    return (
        <>

            <Dropdown onSelect={(evtKey) => handleOnSelect(evtKey)} className="d-grid gap-2">
                <Dropdown.Toggle variant={'outline-secondary'} bg={'light'}>
                    Escolha um recipiente {' '}
                    {icon !== '' && <FontAwesomeIcon icon={['fas', icon]} size='lg' />}
                </Dropdown.Toggle>
                <Dropdown.Menu>
                    {(!loading) ? (
                        <>
                            {waterIntakeContainers?.length > 0 ? (
                                waterIntakeContainers.map((waterIntakeContainer, index) => (
                                    <Dropdown.Item key={waterIntakeContainer.size} eventKey={waterIntakeContainer.size}>
                                        <FontAwesomeIcon icon={['fas', waterIntakeContainer.icon]} /> {waterIntakeContainer.name}
                                    </Dropdown.Item>
                                ))
                            ) : (
                                <Dropdown.Item>Nenhum registro encontrado</Dropdown.Item>
                            )}
                        </>
                    ) : (
                        <Dropdown.Item>Loading...</Dropdown.Item>
                    )}
                </Dropdown.Menu>
            </Dropdown>
        </>
    );
}

export default FormControlWaterIntakeContainerSelect;