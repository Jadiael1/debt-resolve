import { useState } from 'react';
import { useAuth } from '../../../../contexts/AuthContext';

function Dashboard() {
	const [isSidebarOpen, setIsSidebarOpen] = useState(false);
	const { logout } = useAuth();
	const toggleSidebar = () => {
		setIsSidebarOpen(!isSidebarOpen);
	};

	return (
		<div className='bg-gray-100 min-h-screen'>
			{/* Navbar */}
			<nav className='bg-white shadow-lg p-4 flex justify-between items-center'>
				{/* Botão de Toggle para Sidebar em Telas Menores */}
				<button
					onClick={toggleSidebar}
					className='text-xl font-bold md:hidden'
				>
					{/* Ícone do Menu (Exemplo: ícone de hambúrguer) */}☰
				</button>

				<div className='text-xl font-bold'>MyApp</div>

				{/* Informações do Usuário */}
				<div className='flex items-center'>
					<span className='mr-2'>Olá, Usuário</span>
					<button
						className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
						onClick={logout}
					>
						Sair
					</button>
				</div>
			</nav>

			{/* Sidebar */}
			<div
				className={`absolute z-20 w-64 bg-gray-800 min-h-screen text-white transform ${
					isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
				} transition-transform duration-300 ease-in-out md:relative md:translate-x-0`}
			>
				<ul className='p-4'>
					<li className='mb-4'>
						<a
							href='/dashboard'
							className='hover:text-gray-200'
						>
							Dashboard
						</a>
					</li>
					<li className='mb-4'>
						<a
							href='/cobrancas'
							className='hover:text-gray-200'
						>
							Cobranças
						</a>
					</li>
					<li className='mb-4'>
						<a
							href='/parcelas'
							className='hover:text-gray-200'
						>
							Parcelas
						</a>
					</li>
					<li className='mb-4'>
						<a
							href='/perfil'
							className='hover:text-gray-200'
						>
							Perfil
						</a>
					</li>
					{/* Mais itens conforme necessário */}
				</ul>
			</div>

			{/* Conteúdo Principal */}
			<main className={`p-7 ${isSidebarOpen ? 'ml-64' : ''} md:ml-64`}>
				<h1 className='text-2xl font-semibold'>Bem-vindo ao Painel de Controle</h1>
				{/* Outros componentes e conteúdo conforme necessário */}
			</main>
		</div>
	);
}

export default Dashboard;
