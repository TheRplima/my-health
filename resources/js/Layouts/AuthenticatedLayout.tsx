import { PropsWithChildren, ReactNode } from 'react';
import { User } from '@/types';
import CustonImage from '@/Components/CustonImage';
import { Container, Navbar, Nav, NavDropdown } from 'react-bootstrap';
import { route } from 'ziggy-js';
import { usePage } from '@inertiajs/react';

export default function Authenticated({ user, header, children }: PropsWithChildren<{ user: User, header?: ReactNode }>) {

    const { csrf_token } = usePage().props;

    const UserMenu = (
        user.image ? (
            <CustonImage src={'storage/' + user.image} alt={user.name} width={50} className='rounded-circle' />
        ) : (
            user.name
        )
    )

    function handleLogout(e: React.MouseEvent<HTMLAnchorElement>) {
        e.preventDefault();

        const form = document.createElement('form');
        form.action = route('logout');
        form.method = 'POST';
        document.body.appendChild(form);
        form.submit();
    }

    return (
        <div className="min-h-screen bg-gray-100">
            <Navbar collapseOnSelect sticky="top" expand="lg" bg="white" variant="light" className='shadow'>
                <Container>
                    <Navbar.Brand href={route('welcome')} className='ms-lg-5 ms-md-0'>
                        <CustonImage src='/storage/images/logo.png' width={100} />
                    </Navbar.Brand>
                    <Navbar.Toggle aria-controls="responsive-navbar-nav" />
                    <Navbar.Collapse id="responsive-navbar-nav">
                        <Nav className="mr-auto w-100 flex justify-content-end">
                            <Nav.Link href={route('water-intakes.index')}>Consumo de água</Nav.Link>
                            <Nav.Link href="#">Controle de peso</Nav.Link>
                            <Nav.Link href="#">Atividades Físicas</Nav.Link>
                            <NavDropdown title="Relatórios" id="collasible-nav-dropdown">
                                <NavDropdown.Item href="#">Consumo de água</NavDropdown.Item>
                                <NavDropdown.Item href="#">Controle de peso</NavDropdown.Item>
                                <NavDropdown.Item href="#">Atividades Físicas</NavDropdown.Item>
                            </NavDropdown>
                        </Nav>
                        <Nav className='me-5'>
                            <NavDropdown title={UserMenu} id="profile-dropdown" drop={'start'}>
                                <NavDropdown.Item href="#">Registrar consumo de água</NavDropdown.Item>
                                <NavDropdown.Item href="#">Registrar peso</NavDropdown.Item>
                                <NavDropdown.Item href="#">Registrar atividade física</NavDropdown.Item>
                                <NavDropdown.Item href={route('profile.edit')}>Perfil</NavDropdown.Item>
                                <NavDropdown.Divider />
                                <form method="post" action={route('logout')}>
                                    <NavDropdown.Item as={'button'} style={{ 'color': 'red' }}>Sair</NavDropdown.Item>
                                    <input type="hidden" name="_token" value={csrf_token as string} />
                                </form>
                            </NavDropdown>
                        </Nav>
                    </Navbar.Collapse>
                </Container>
            </Navbar>

            <main>
                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 text-gray-900">
                                {header && (
                                    <header>
                                        <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{header}</div>
                                    </header>
                                )}
                                <div className="flex items-center justify-between mb-2">
                                    {children}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
}
