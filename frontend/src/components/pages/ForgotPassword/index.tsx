// PasswordResetRequest.tsx
import React, { useState } from 'react';
import backgroundImage from '../../../assets/bg-password-reset-request.png';
import { useNavigate } from 'react-router-dom';

const PasswordResetRequest = () => {
	const [email, setEmail] = useState('');
	const [loading, setLoading] = useState(false);
	const [message, setMessage] = useState('');
	const navigate = useNavigate();

	const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
		event.preventDefault();
		setLoading(true);
		setMessage('');

		try {
			const response = await fetch('https://api.debtscrm.shop/api/v1/auth/forgot-password', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					accept: 'application/json',
				},
				body: JSON.stringify({ email }),
			});

			const data = await response.json();
			if (response.ok) {
				setMessage('Verifique seu email para instruções de redefinição de senha.');
			} else {
				setMessage(data.message || 'Erro ao solicitar redefinição de senha.');
			}
		} catch (error) {
			setMessage('Erro de conexão com o servidor.');
		} finally {
			setLoading(false);
			setEmail('');
		}
	};

	const handleGoBack = () => {
		navigate('/signin');
	};

	return (
		<div
			className='flex flex-col items-center justify-center min-h-screen bg-no-repeat bg-cover bg-center'
			style={{ backgroundImage: `url(${backgroundImage})` }}
		>
			<div className='p-8 bg-white bg-opacity-95 shadow-2xl rounded-2xl max-w-lg w-full space-y-8'>
				<h2 className='text-4xl font-bold text-center text-gray-900 mb-10'>Redefinição de Senha</h2>
				<form
					onSubmit={handleSubmit}
					className='space-y-6'
				>
					<div>
						<label
							htmlFor='email'
							className='block text-sm font-medium text-gray-700'
						>
							Email
						</label>
						<input
							type='email'
							id='email'
							name='email'
							required
							value={email}
							onChange={e => setEmail(e.target.value)}
							className='mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300 ease-in-out'
						/>
					</div>
					<button
						type='submit'
						disabled={loading}
						className={`w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white ${
							loading ? 'bg-gray-300' : 'bg-blue-600 hover:bg-blue-700'
						} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 ease-in-out`}
					>
						{loading ? 'Enviando...' : 'Solicitar Redefinição'}
					</button>
				</form>
				{message && <div className='mt-5 text-center text-sm font-medium text-gray-600'>{message}</div>}
				<button
					onClick={handleGoBack}
					className='mt-4 text-sm text-blue-600 hover:text-blue-800 transition duration-200 ease-in-out'
				>
					Voltar para a página de login
				</button>
			</div>
		</div>
	);
};

export default PasswordResetRequest;
