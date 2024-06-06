import React, { useState } from 'react';
import Alert from 'react-bootstrap/Alert';

interface Props {
    variant: string;
    heading?: string | null;
    message: string;
    duration?: number;
}

export default function AlertDismissible(props: Props) {
    const [show, setShow] = useState(true);
    const type = props.variant === 'success' ? 'success' : 'danger';

    const duration = props.duration || 5000;

    setTimeout(function () {
        setShow(false);
    }, duration);

    if (show) {
        return (
            <Alert variant={type} onClose={() => setShow(false)} show={show} dismissible transition={true} style={{ position: "absolute", top: 100, right: 0, zIndex: 999, opacity: .9 }} >
                {props.heading && <Alert.Heading>{props.heading}</Alert.Heading>}
                <p>
                    {props.message}
                </ p>
            </Alert>
        );
    }
}
