import { useNavigate } from 'react-router-dom';
import Sidebar from '../../../organisms/Sidebar';

function Dashboard() {
	const navigate = useNavigate();

	return (
		<Sidebar>
			<h1 className='text-2xl font-semibold mb-6'>Bem-vindo ao Painel de Controle</h1>

			<div className='grid md:grid-cols-2 gap-4'>
				<div
					className='bg-white rounded-lg shadow p-6 hover:shadow-md transition duration-300 ease-in-out cursor-pointer'
					onClick={() => navigate('/dashboard/charge/create')}
				>
					<h2 className='text-lg font-semibold'>Criar Cobrança</h2>
					<p className='text-gray-600 mt-2'>Inicie o processo para criar uma nova cobrança.</p>
				</div>

				<div
					className='bg-white rounded-lg shadow p-6 hover:shadow-md transition duration-300 ease-in-out cursor-pointer'
					onClick={() => navigate('/dashboard/charges')}
				>
					<h2 className='text-lg font-semibold'>Cobranças Ativas</h2>
					<p className='text-gray-600 mt-2'>Veja suas cobranças ativas e gerencie-as.</p>
				</div>
			</div>
		</Sidebar>
	);
}

export default Dashboard;
