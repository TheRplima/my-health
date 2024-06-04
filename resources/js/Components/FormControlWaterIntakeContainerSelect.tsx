import React, { useState, useEffect } from 'react'
import { usePage } from '@inertiajs/react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { IconName } from '@fortawesome/fontawesome-common-types';
import { Dropdown } from 'react-bootstrap';
import { PageProps } from '@/types';

interface Props {
    setAmount: (amount: number | string) => void;
    toReport?: boolean;
}
interface Icon {
    name?: string;
    icon: string | 0;
}
interface WaterIntakeContainer {
    id: number;
    name: string;
    size: number;
    icon: string;
    active: number;
}

export default function FormControlWaterIntakeContainerSelect(props: Props) {
    const { auth } = usePage<PageProps>().props;
    const [waterIntakeContainers, setWaterIntakeContainers] = useState<WaterIntakeContainer[] | []>(auth?.waterIntakeContainers || []);
    const [loading, setLoading] = useState(true);
    const [icon, setIcon] = useState<Icon>({ icon: 0, name: 'Escolha um recipiente' });
    library.add(fas);

    const handleOnSelect = (evtKey: number) => {
        const container = waterIntakeContainers?.find((container) => { return container.size == evtKey });
        setIcon({ icon: props.toReport && evtKey == 0 ? 0 : (container?.icon ?? 'glass-water'), name: container?.name ?? 'Escolha um recipiente' });
        props.setAmount(evtKey);
    }

    useEffect(() => {
        async function loadStorageData() {
            if (auth?.waterIntakeContainers) {
                setWaterIntakeContainers(auth?.waterIntakeContainers);
                setLoading(false);
            }
        }

        loadStorageData();
        return () => {
            setWaterIntakeContainers([]);
            setLoading(false);
        }
    }, [auth?.waterIntakeContainers]);

    return (
        <>

            <Dropdown onSelect={(evtKey: any) => handleOnSelect(evtKey)} className="d-grid gap-2">
                <Dropdown.Toggle variant={'outline-secondary'}>
                    {icon.name + ' '}
                    {icon.icon !== 0 && <FontAwesomeIcon icon={['fas', icon.icon as IconName]} size='lg' />}
                    {(icon.icon === 0 && props.toReport === true) && '(Todos)'}
                </Dropdown.Toggle>
                <Dropdown.Menu>
                    {(!loading) ? (
                        <>
                            {props.toReport ? (
                                <Dropdown.Item eventKey={0}>Todos</Dropdown.Item>

                            ) : null}
                            {waterIntakeContainers.length > 0 ? (
                                waterIntakeContainers.map((waterIntakeContainer) => (
                                    <Dropdown.Item key={waterIntakeContainer.size} eventKey={waterIntakeContainer.size}>
                                        <FontAwesomeIcon icon={['fas', waterIntakeContainer.icon as IconName]} /> {waterIntakeContainer.name}
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
