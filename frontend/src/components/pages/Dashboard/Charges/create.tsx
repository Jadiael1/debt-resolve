import { useState } from 'react';
import { useAuth } from '../../../../contexts/AuthContext';
import Sidebar from '../../../organisms/Sidebar';

function CreateCharge() {
	const { token, user } = useAuth();
	type TChargeData = {
		name: string;
		description: string;
		amount: number;
		installments_number: number;
		due_day: number;
		collector_id?: number;
		debtor_id?: number;
		role?: string | null;
	};
	const [chargeData, setChargeData] = useState<TChargeData>({
		name: '',
		description: '',
		amount: 0,
		installments_number: 0,
		due_day: 0,
		collector_id: 0,
		debtor_id: 0,
		role: '',
	});

	const [message, setMessage] = useState({ type: '', content: '' });

	const handleChange = (evt: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
		const newChargeData =
			evt.target.name === 'role' && evt.target.value === 'collector' ?
				{ ...chargeData, ...{ collector_id: user?.id as number, debtor_id: 0, [evt.target.name]: evt.target.value } }
			:	{ ...chargeData, ...{ debtor_id: user?.id as number, collector_id: 0, [evt.target.name]: evt.target.value } };
		setChargeData(newChargeData);
	};

	const handleSubmit = async (evt: React.FormEvent<HTMLFormElement>) => {
		evt.preventDefault();
		delete chargeData.role;
		chargeData.collector_id === 0 ? delete chargeData.collector_id : delete chargeData.debtor_id;
		try {
			const request = await fetch('https://api.debtscrm.shop/api/v1/charges', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Authorization: `Bearer ${token}`,
				},
				body: JSON.stringify(chargeData),
			});
			if (request.ok) {
				setMessage({ type: 'success', content: 'Cobrança criada com sucesso!' });
			} else {
				setMessage({ type: 'error', content: 'Erro ao criar cobrança. Por favor, tente novamente.' });
			}
		} catch (error) {
			setMessage({ type: 'error', content: 'Ocorreu um erro de rede. Por favor, tente novamente.' });
		}
	};

	return (
		<Sidebar>
			<div className='container mx-auto p-4'>
				<h1 className='text-2xl font-semibold mb-4'>Criar Nova Cobrança</h1>

				{/* Mensagem de Sucesso ou Erro */}
				{message.content && (
					<div
						className={`mb-4 text-center p-3 rounded-lg text-white ${
							message.type === 'success' ? 'bg-green-500' : 'bg-red-500'
						}`}
					>
						{message.content}
					</div>
				)}

				<form
					onSubmit={handleSubmit}
					className='bg-white p-4 rounded-lg shadow'
				>
					<div className='mb-4'>
						<label
							htmlFor='name'
							className='block mb-2 text-sm font-bold text-gray-700'
						>
							Nome da Cobrança
						</label>
						<input
							type='text'
							id='name'
							name='name'
							onChange={handleChange}
							value={chargeData.name}
							className='w-full p-2 border border-gray-300 rounded shadow-sm'
						/>
					</div>

					<div className='mb-4'>
						<label
							htmlFor='description'
							className='block mb-2 text-sm font-bold text-gray-700'
						>
							Descrição
						</label>
						<input
							type='text'
							id='description'
							name='description'
							onChange={handleChange}
							value={chargeData.description}
							className='w-full p-2 border border-gray-300 rounded shadow-sm'
						/>
					</div>

					<div className='mb-4'>
						<label
							htmlFor='amount'
							className='block mb-2 text-sm font-bold text-gray-700'
						>
							Valor
						</label>
						<input
							type='number'
							id='amount'
							name='amount'
							onChange={handleChange}
							value={chargeData.amount}
							className='w-full p-2 border border-gray-300 rounded shadow-sm'
						/>
					</div>

					<div className='mb-4'>
						<label
							htmlFor='installments_number'
							className='block mb-2 text-sm font-bold text-gray-700'
						>
							Número de Parcelas
						</label>
						<input
							type='number'
							id='installments_number'
							name='installments_number'
							onChange={handleChange}
							value={chargeData.installments_number}
							className='w-full p-2 border border-gray-300 rounded shadow-sm'
						/>
					</div>

					<div className='mb-4'>
						<label
							htmlFor='due_day'
							className='block mb-2 text-sm font-bold text-gray-700'
						>
							Dia de Vencimento
						</label>
						<input
							type='number'
							id='due_day'
							name='due_day'
							onChange={handleChange}
							value={chargeData.due_day}
							className='w-full p-2 border border-gray-300 rounded shadow-sm'
						/>
					</div>

					<div className='mb-4'>
						<label
							htmlFor='role'
							className='block mb-2 text-sm font-bold text-gray-700'
						>
							Você é:
						</label>
						<select
							id='role'
							name='role'
							onChange={handleChange}
							value={chargeData.role as string}
							className='w-full p-2 border border-gray-300 rounded shadow-sm'
						>
							<option value='collector'>Cobrador</option>
							<option value='debtor'>Devedor</option>
						</select>
					</div>

					<button
						type='submit'
						className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
					>
						Criar Cobrança
					</button>
				</form>
			</div>
		</Sidebar>
	);
}

export default CreateCharge;
