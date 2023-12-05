import React, { useState, useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import backgroundImage from '../../../assets/bg-reset-password.png';

const ResetPassword = () => {
	const [searchParams] = useSearchParams();
	const [token, setToken] = useState('');
	const [email, setEmail] = useState('');
	const [password, setPassword] = useState('');
	const [passwordConfirmation, setPasswordConfirmation] = useState('');
	const [message, setMessage] = useState('');
	const [error, setError] = useState('');
	const navigate = useNavigate();

	useEffect(() => {
		const tokenFromUrl = searchParams.get('token');
		if (tokenFromUrl) {
			setToken(tokenFromUrl);
		} else {
			setError('Token de redefinição de senha não encontrado.');
		}
	}, [searchParams]);

	const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
		event.preventDefault();
		setError('');
		setMessage('');

		if (password !== passwordConfirmation) {
			setError('As senhas não coincidem.');
			return;
		}

		try {
			const response = await fetch('https://api.debtscrm.shop/api/v1/auth/reset-password', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					accept: 'application/json',
				},
				body: JSON.stringify({ token, email, password, password_confirmation: passwordConfirmation }),
			});

			const data = await response.json();
			if (response.ok) {
				setMessage('Sua senha foi redefinida com sucesso.');
			} else {
				setError(data.message || 'Erro ao redefinir a senha.');
			}
		} catch (error) {
			setError('Erro de conexão com o servidor.');
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
			<div className='p-6 bg-white bg-opacity-90 shadow-md rounded-lg max-w-md w-full'>
				<h2 className='text-2xl font-semibold text-center text-gray-700 mb-4'>Redefinir Senha</h2>
				<form
					onSubmit={handleSubmit}
					className='space-y-4'
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
							value={email}
							onChange={e => setEmail(e.target.value)}
							className='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500'
							required
						/>
					</div>
					<div>
						<label
							htmlFor='password'
							className='block text-sm font-medium text-gray-700'
						>
							Nova Senha
						</label>
						<input
							type='password'
							id='password'
							value={password}
							onChange={e => setPassword(e.target.value)}
							className='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500'
							required
						/>
					</div>
					<div>
						<label
							htmlFor='passwordConfirmation'
							className='block text-sm font-medium text-gray-700'
						>
							Confirmação de Senha
						</label>
						<input
							type='password'
							id='passwordConfirmation'
							value={passwordConfirmation}
							onChange={e => setPasswordConfirmation(e.target.value)}
							className='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500'
							required
						/>
					</div>
					<button
						type='submit'
						className='w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
					>
						Redefinir Senha
					</button>
				</form>
				{message && <div className='mt-3 text-sm font-medium text-green-600'>{message}</div>}
				{error && <div className='mt-3 text-sm font-medium text-red-600'>{error}</div>}
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

export default ResetPassword;
