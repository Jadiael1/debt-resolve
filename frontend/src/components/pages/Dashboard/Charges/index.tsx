import { useState, useEffect } from 'react';
import { useAuth } from '../../../../contexts/AuthContext';
import { FaInfoCircle, FaPlus, FaRegMoneyBillAlt } from 'react-icons/fa';
import Sidebar from '../../../organisms/Sidebar';
import { useNavigate } from 'react-router-dom';
import './listCharges.css';

type TChargeData = {
	id: number;
	name: string;
	description: string;
	amount: number;
	installments_number: number;
	due_day: number;
	collector_id?: number;
	debtor_id?: number;
	role?: string | null;
};
type TChargesData = {
	debtors: TChargeData[];
	collectors: TChargeData[];
};

function ListCharges() {
	const { token } = useAuth();
	const [charges, setCharges] = useState<TChargesData | null>(null);
	const [loading, setLoading] = useState(true);
	const navigate = useNavigate();

	useEffect(() => {
		const fetchCharges = async () => {
			try {
				const response = await fetch('https://api.debtscrm.shop/api/v1/users/charges', {
					method: 'GET',
					headers: {
						Authorization: `Bearer ${token}`,
						'Content-Type': 'application/json',
					},
				});
				const data = await response.json();
				if (data.status === 'success') {
					setCharges(data.data);
				}
				setLoading(false);
			} catch (error) {
				setLoading(false);
			}
		};
		fetchCharges();
	}, [token]);

	const renderChargeSection = (title: string, charges: TChargeData[], role: string) => {
		return (
			<div className='w-full md:w-1/2 px-2 mb-4'>
				<h2 className='text-xl font-semibold mb-2'>{title}</h2>
				{charges.length > 0 ?
					<div className='grid grid-cols-1 gap-4'>
						{charges.map(charge => (
							<ChargeCard
								key={charge.id}
								charge={charge}
							/>
						))}
					</div>
				:	<div className='bg-white p-4 rounded-lg shadow text-center'>
						<p>Você ainda não possui cobranças como {role === 'debtor' ? 'devedor' : 'cobrador'}.</p>
						<div className='flex items-center justify-center'>
							<button
								onClick={() => navigate('/dashboard/charge/create')}
								className='mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center'
							>
								<FaPlus className='mr-2' />
								Criar Cobrança
							</button>
						</div>
					</div>
				}
			</div>
		);
	};

	return (
		<Sidebar>
			<div className='container mx-auto p-4'>
				<h1 className='text-2xl font-semibold mb-10'>Suas Cobranças</h1>
				{loading || !charges ?
					<div className='animate-pulse'>
						<div className='h-8 bg-gray-300 rounded mb-4'></div>
						<div className='grid grid-cols-1 md:grid-cols-2 gap-4'>
							<div className='h-48 bg-gray-300 rounded'></div>
							<div className='h-48 bg-gray-300 rounded'></div>
						</div>
					</div>
				:	<div className='flex flex-wrap -mx-2'>
						{renderChargeSection('Cobranças Como Devedor', charges.debtors, 'debtor')}
						{renderChargeSection('Cobranças Como Cobrador', charges.collectors, 'collector')}
					</div>
				}
			</div>
		</Sidebar>
	);
}

function ChargeCard({ charge }: { charge: TChargeData }) {
	const navigate = useNavigate();
	return (
		<div className='bg-white rounded-lg shadow p-4 hover:shadow-md transition duration-300 ease-in-out h-full flex flex-col'>
			<h3 className='text-lg font-semibold line-clamp-1'>{charge.name}</h3>
			<p className='text-gray-600 flex-grow line-clamp-1'>{charge.description}</p>
			<div className='flex md:flex-row flex-col justify-between items-center mt-4'>
				<div className='flex items-center text-green-600 mb-2 md:mb-0'>
					<FaRegMoneyBillAlt className='mr-2' />
					<span>{`R$ ${charge.amount}`}</span>
				</div>
				<button
					onClick={() => navigate(`/dashboard/charge/${charge.id}`)}
					className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded flex items-center'
				>
					<FaInfoCircle className='mr-2' />
					Detalhes
				</button>
			</div>
		</div>
	);
}

export default ListCharges;
