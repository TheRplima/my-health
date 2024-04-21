import React, { useState, useEffect } from 'react'
import Form from 'react-bootstrap/Form';
import { useAuth } from "../hooks/auth";
import useWaterIntakeContainerData from '../services/useWaterIntakeContainerData';
import Spinner from 'react-bootstrap/Spinner';

function FormControlWaterIntakeContainerSelect(props) {
    const { waterIntakeContainerData } = useWaterIntakeContainerData();
    const { cookies } = useAuth();
    const [waterIntakeContainers, setWaterIntakeContainers] = useState(null);
    const [loading, setLoading] = useState(true);

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
            <Form.Select onChange={e => props.setAmount(e.target.value)}>
                <option value={0} key={-1}>Escolha um recipiente</option>
                {(!loading) ? (
                    <>
                        {waterIntakeContainers?.length > 0 ? (
                            waterIntakeContainers.map((waterIntakeContainer, index) => (
                                <option value={waterIntakeContainer.size} key={index}>{waterIntakeContainer.name}</option>
                            ))
                        ) : (
                            <option>Nenhum registro encontrado</option>
                        )}
                    </>
                ) : (
                    <option>
                        <Spinner animation="border" role="status">
                            <span className="sr-only">Loading...</span>
                        </Spinner>
                    </option>
                )}
            </Form.Select>
        </>
    );
}

export default FormControlWaterIntakeContainerSelect;