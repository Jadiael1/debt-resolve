import { useState } from 'react';
import { useAuth } from '../../../../contexts/AuthContext';
import Sidebar from '../../../organisms/Sidebar';
import { useNavigate } from 'react-router-dom';
import { FaEye, FaSpinner } from 'react-icons/fa';

function CreateCharge() {
	const { token, user } = useAuth();
	const [showForm, setShowForm] = useState<boolean>(true);
	const navigate = useNavigate();
	const [message, setMessage] = useState({ type: '', content: '', redirect: '' });
	const [loading, setLoading] = useState(false);
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

	const handleChange = (evt: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
		const newChargeData =
			evt.target.name === 'role' && evt.target.value === 'collector' ?
				{ ...chargeData, ...{ collector_id: user?.id as number, debtor_id: 0, [evt.target.name]: evt.target.value } }
			:	{ ...chargeData, ...{ debtor_id: user?.id as number, collector_id: 0, [evt.target.name]: evt.target.value } };
		setChargeData(newChargeData);
	};

	const handleSubmit = async (evt: React.FormEvent<HTMLFormElement>) => {
		evt.preventDefault();
		setLoading(true);
		delete chargeData.role;
		chargeData.collector_id === 0 ? delete chargeData.collector_id : delete chargeData.debtor_id;
		try {
			const response = await fetch('https://api.debtscrm.shop/api/v1/charges', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Authorization: `Bearer ${token}`,
				},
				body: JSON.stringify(chargeData),
			});
			const data = await response.json();
			if (response.ok && response.status === 201 && data.message === 'Charge successfully created') {
				setMessage({
					type: 'success',
					content: 'Cobrança criada com sucesso!',
					redirect: `/dashboard/charge/${data.data.charge.id}`,
				});
				setShowForm(false);
			}
			if (response.status === 500 && data.message === 'Error occurred while saving models to the database.') {
				setMessage({ type: 'error', content: 'Ocorreu um erro ao salvar a cobrança no banco de dados.', redirect: '' });
				evt.currentTarget.reset();
			}
			if (response.status === 500 && data.message === 'Unexpected error when creating resource') {
				setMessage({ type: 'error', content: 'Erro inesperado ao criar recurso', redirect: '' });
				evt.currentTarget.reset();
			}
			if (response.status === 409 && data.message === 'This charge already exists') {
				setMessage({ type: 'error', content: 'Esta cobrança já existe', redirect: '' });
				evt.currentTarget.reset();
			}
			if (
				response.status === 422 &&
				data.message === 'You need to choose whether you are the collector or the debtor of the charge'
			) {
				setMessage({
					type: 'error',
					content: 'Você precisa escolher se é o cobrador ou o devedor da cobrança',
					redirect: '',
				});
				evt.currentTarget.reset();
			}
		} catch (error) {
			setMessage({ type: 'error', content: 'Ocorreu um erro de rede. Por favor, tente novamente.', redirect: '' });
		}
	};

	return (
		<Sidebar>
			<div className='mx-auto p-4'>
				{showForm && <h1 className='text-2xl font-semibold mb-4'>Criar Nova Cobrança</h1>}
				{message.content && (
					<div className='flex justify-center h-screen'>
						<div>
							<div
								className={`text-center p-3 rounded-t-lg text-white ${
									message.type === 'success' ? 'bg-green-500' : 'bg-red-500'
								}`}
							>
								{message.content}
							</div>
							{message.redirect && (
								<div
									className='text-center rounded-b-lg text-white bg-blue-500 hover:bg-blue-600 cursor-pointer flex justify-center items-center p-3'
									onClick={() => navigate(message.redirect)}
								>
									Ver Cobrança
									<FaEye className='ml-2' />
								</div>
							)}
						</div>
					</div>
				)}
				{showForm && (
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
							className={`${
								loading ? 'bg-gray-500 cursor-wait' : 'bg-blue-500 hover:bg-blue-700'
							} text-white font-bold py-2 px-4 rounded flex items-center justify-center`}
							disabled={loading ? true : false}
						>
							Criar Cobrança
							{loading && <FaSpinner className='animate-spin ml-2' />}
						</button>
					</form>
				)}
			</div>
		</Sidebar>
	);
}

export default CreateCharge;
