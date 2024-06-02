import { Link, Head } from '@inertiajs/react';
import { PageProps } from '@/types';
import { route } from 'ziggy-js';
import CustonImage from '@/Components/CustonImage';

import { Col, Container, Row, Card, Button } from 'react-bootstrap';

export default function Welcome({ auth, laravelVersion, phpVersion }: PageProps<{ laravelVersion: string, phpVersion: string }>) {
    return (
        <>
            <Head title="Bem vindo" />
            <div className="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                <div className="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
                    {auth.user ? (
                        <Link
                            href={route('dashboard')}
                            className="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={route('login')}
                                className="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >
                                Log in
                            </Link>

                            <Link
                                href={route('register')}
                                className="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >
                                Register
                            </Link>
                        </>
                    )}
                </div>

                <div className="max-w-7xl mx-auto p-0 lg:p-8">
                    <div className="mt-lg-5">
                        <Container className='mx-0 px-0'>
                            <Row>
                                <Col className='text-center'>
                                    <Card>
                                        <Card.Body>
                                            <div className="flex justify-center mb-5">
                                                <CustonImage src='/storage/images/logo.png' className="w-32 h-32 sm:w-48 sm:h-48 text-gray-900 dark:text-white" />
                                            </div>

                                            <Card className="mb-5" border="light">
                                                <Card.Img variant="top" src="/storage/images/minha-saude-welcome.jpg" />
                                                <Card.ImgOverlay className='d-none d-lg-flex text-white'>
                                                    <Container className='align-self-center bg-dark bg-opacity-25 rounded p-5 mx-5'>
                                                        <Card.Title><h1>Bem-vindo ao Minha Saúde</h1></Card.Title>
                                                        <Card.Text>Seu sistema completo de gerenciamento de saúde pessoal.<br />Nossa missão é fornecer uma ferramenta fácil de usar e eficaz para ajudá-lo a manter e melhorar sua saúde e bem-estar.<br />Esperamos que o Minha Saúde se torne seu parceiro de confiança na jornada para uma vida mais saudável.<br />Junte-se a nós e comece a transformar sua saúde hoje!</Card.Text>
                                                        <Button variant="primary" href={route('register')}>Comece agora</Button>
                                                    </Container>
                                                </Card.ImgOverlay>
                                                <Card.Body className='d-lg-none'>
                                                    <Card.Title><h1>Bem-vindo ao Minha Saúde</h1></Card.Title>
                                                    <Card.Text>Seu sistema completo de gerenciamento de saúde pessoal.<br />Nossa missão é fornecer uma ferramenta fácil de usar e eficaz para ajudá-lo a manter e melhorar sua saúde e bem-estar.<br />Esperamos que o Minha Saúde se torne seu parceiro de confiança na jornada para uma vida mais saudável.<br />Junte-se a nós e comece a transformar sua saúde hoje!</Card.Text>
                                                    <Button variant="primary" href={route('register')}>Comece agora</Button>
                                                </Card.Body>
                                            </Card>
                                            <Card className="mb-5" border="light">
                                                <Card.Body>
                                                    <Card.Title><h2>O que é o Minha Saúde?</h2></Card.Title>
                                                    <Card.Text>O Minha Saúde é um sistema de gerenciamento de saúde pessoal projetado para ajudá-lo a acompanhar e melhorar vários aspectos da sua saúde. Com nosso aplicativo, você pode:</Card.Text>
                                                    <Row>
                                                        <Col lg={6}>
                                                            <Card className="mb-5" border="light">
                                                                <Card.Img variant="top" src="/storage/images/ingestao-agua.jpg" />
                                                                <Card.Body>
                                                                    <Card.Title><h3>Acompanhar sua ingestão de água</h3></Card.Title>
                                                                    <Card.Text>Manter-se hidratado é essencial para a saúde geral. O Minha Saúde permite que você registre sua ingestão diária de água, ajudando a garantir que você esteja atingindo suas metas de hidratação.</Card.Text>
                                                                </Card.Body>
                                                            </Card>
                                                        </Col>
                                                        <Col lg={6}>
                                                            <Card className="mb-5" border="light">
                                                                <Card.Img variant="top" src="/storage/images/controle-peso.jpg" />
                                                                <Card.Body>
                                                                    <Card.Title><h3>Controlar seu peso</h3></Card.Title>
                                                                    <Card.Text>Monitorar o peso é uma parte importante de um estilo de vida saudável. Com nosso sistema, você pode registrar e acompanhar seu peso ao longo do tempo, ajudando a identificar tendências e fazer ajustes conforme necessário para alcançar seus objetivos de saúde.</Card.Text>
                                                                </Card.Body>
                                                            </Card>
                                                        </Col>
                                                    </Row>
                                                    <Row>
                                                        <Col lg={6}>
                                                            <Card className="mb-5" border="light">
                                                                <Card.Img variant="top" src="/storage/images/atividades-fisicas.jpg" />
                                                                <Card.Body>
                                                                    <Card.Title><h3>Registrar atividades físicas</h3></Card.Title>
                                                                    <Card.Text>A atividade física regular é crucial para a saúde e bem-estar. O Minha Saúde permite que você registre suas atividades físicas, desde caminhadas diárias até treinos intensivos, para que você possa acompanhar seu progresso e manter-se motivado.</Card.Text>
                                                                </Card.Body>
                                                            </Card>
                                                        </Col>
                                                        <Col lg={6}>
                                                            <Card className="mb-5" border="light">
                                                                <Card.Img variant="top" src="/storage/images/monitorar-saude.jpg" />
                                                                <Card.Body>
                                                                    <Card.Title><h3>Monitorar sua saúde em geral</h3></Card.Title>
                                                                    <Card.Text>Além de rastrear água, peso e atividades físicas, o Minha Saúde oferece ferramentas para monitorar outros aspectos importantes da sua saúde. Isso pode incluir o registro de sinais vitais, padrões de sono, hábitos alimentares e muito mais. Nosso objetivo é fornecer uma visão abrangente da sua saúde, permitindo que você tome decisões informadas e proativas.</Card.Text>
                                                                </Card.Body>
                                                            </Card>
                                                        </Col>
                                                    </Row>
                                                </Card.Body>
                                            </Card>
                                            <Card className="mb-5" border="light">
                                                <Card.Body>
                                                    <Card.Title><h2>Nossa Visão</h2></Card.Title>
                                                    <Card.Text>No Minha Saúde, acreditamos que a tecnologia pode ser uma poderosa aliada na busca por uma vida mais saudável. Queremos capacitar nossos usuários com as informações e ferramentas necessárias para gerenciar sua saúde de forma eficaz e alcançar seus objetivos de bem-estar.</Card.Text>
                                                </Card.Body>
                                            </Card>
                                            <Card className="mb-5" border="light">
                                                <Card.Body>
                                                    <Card.Title><h2>Fale Conosco</h2></Card.Title>
                                                    <Card.Text>Estamos sempre trabalhando para melhorar o Minha Saúde e adoramos ouvir a opinião de nossos usuários. Se você tiver alguma dúvida, sugestão ou feedback, por favor, entre em contato conosco. Sua opinião é muito importante para nós!</Card.Text>
                                                </Card.Body>
                                            </Card>
                                        </Card.Body>
                                    </Card>
                                </Col>
                            </Row>
                        </Container>
                    </div>

                </div>
            </div>
        </>
    );
}
