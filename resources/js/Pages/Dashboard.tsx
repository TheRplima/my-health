import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import { Col, Container, Row } from 'react-bootstrap';

import CardWaterIntakeToday from '@/Components/CardWaterIntakeToday';
import CardLatestWeightControl from '@/Components/CardLatestWeightControl';
import CardThisWeekPhysicalActivity from '@/Components/CardThisWeekPhysicalActivities';
import WaterIntakeChart from '@/Components/WaterIntakeChart';
import WeightControlChart from '@/Components/WeightControlChart';

export default function Dashboard({ auth }: PageProps) {

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight mb-4">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <Container>
                <Row>
                    <Col lg={6} className='mb-3 d-flex align-items-stretch'>
                        <WaterIntakeChart data={auth.weeklyWaterIntakeChartData} title={'Consumo de água na semana'} hAxisTitle={'Dia'} />
                    </Col>
                    <Col lg={6} className='mb-3 d-flex align-items-stretch'>
                        <WaterIntakeChart data={auth.monthlyWaterIntakeChartData} title={'Consumo de água na semana'} hAxisTitle={'Semana'} />
                    </Col>
                </Row>
                <Row>
                    <Col className='mb-3 d-flex align-items-stretch'>
                        <WeightControlChart data={auth.thisYearBodyWeightVariationChartData} title={'Variação do peso corporal ao longo do ano'} hAxisTitle={'Dia'} />
                    </Col>
                </Row>
                <Row>
                    <Col className='mb-3 d-flex align-items-stretch'>
                        <CardWaterIntakeToday user={auth.user} waterIntakes={auth.waterIntakes} totalWaterIntake={auth.totalWaterIntake} />
                    </Col>
                    <Col className='mb-3 d-flex align-items-stretch'>
                        <CardLatestWeightControl user={auth.user} weightControls={auth.weightControls} />
                    </Col>
                </Row>
                <Row>
                    <Col className='mb-3 d-flex align-items-stretch'>
                        <CardThisWeekPhysicalActivity user={auth.user} physicalActivities={auth.physicalActivities} />
                    </Col>
                </Row>
            </Container>
        </AuthenticatedLayout >
    );
}
