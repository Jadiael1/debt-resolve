import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import bgRegister from '../../../assets/bglogin.png';
import NavItem from '../../atoms/NavItem';

const Register = () => {
	const [name, setName] = useState('');
	const [email, setEmail] = useState('');
	const [password, setPassword] = useState('');
	const [confirmPassword, setConfirmPassword] = useState('');
	const [acceptTerms, setAcceptTerms] = useState(false);
	const [errorMessage, setErrorMessage] = useState('');
	const [isSuccess, setIsSuccess] = useState(false);
	const [isLoading, setIsLoading] = useState(false);
	const navigate = useNavigate();

	useEffect(() => {
		document.title = 'DebtsResolve - Registro';
	}, []);

	useEffect(() => {
		if (password && confirmPassword && password === confirmPassword && errorMessage === 'As senhas não coincidem!') {
			setErrorMessage('');
		}
	}, [password, confirmPassword, errorMessage]);

	const handleSubmit = async (event: React.FormEvent) => {
		event.preventDefault();

		if (!acceptTerms) {
			setErrorMessage('Por favor, aceite os termos e condições para se registrar.');
			return;
		}

		if (password !== confirmPassword) {
			setErrorMessage('As senhas não coincidem!');
			return;
		}

		setIsLoading(true);

		try {
			const response = await fetch('https://api.debtscrm.shop/api/v1/auth/signup', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Accept: 'application/json',
				},
				body: JSON.stringify({ name, email, password, password_confirmation: confirmPassword }),
			});

			setIsLoading(false);

			if (!response.ok) {
				throw new Error('Falha ao registrar');
			}
			setErrorMessage('');
			setIsSuccess(true);
		} catch (error) {
			setErrorMessage('Erro no registro.');
			setIsLoading(false);
			console.error('Erro no registro:', error);
		}
	};

	const handleBack = () => {
		navigate(-1);
	};

	return (
		<div
			className='flex items-center justify-center min-h-screen bg-fixed bg-no-repeat bg-cover bg-center'
			style={{ backgroundImage: `url(${bgRegister})` }}
		>
			<div className='w-full max-w-sm md:max-w-md lg:max-w-lg bg-white bg-opacity-95 rounded-xl shadow-xl p-6 border border-gray-300 sm:p-8'>
				<button
					onClick={handleBack}
					className='text-blue-600 hover:text-blue-700 text-sm font-medium mb-4'
				>
					← Voltar
				</button>
				{!isSuccess && <h2 className='text-3xl font-bold text-gray-800 text-center mb-4'>Crie Sua Conta</h2>}
				{errorMessage && (
					<div
						className='bg-red-100 border-l-4 border-red-500 text-red-700 p-4'
						role='alert'
					>
						<p className='font-bold'>Atenção</p>
						<p>{errorMessage}</p>
					</div>
				)}
				{!isSuccess && (
					<form
						onSubmit={handleSubmit}
						className='space-y-4'
					>
						<input
							type='text'
							id='name'
							className='block w-full pl-4 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent'
							placeholder='Nome'
							value={name}
							onChange={e => setName(e.target.value)}
							required
						/>
						<input
							type='email'
							id='email'
							className='block w-full pl-4 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent'
							placeholder='E-mail'
							value={email}
							onChange={e => setEmail(e.target.value)}
							required
						/>
						<input
							type='password'
							id='password'
							className='block w-full pl-4 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent'
							placeholder='Senha'
							value={password}
							onChange={e => setPassword(e.target.value)}
							required
						/>
						<input
							type='password'
							id='confirmPassword'
							className='block w-full pl-4 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent'
							placeholder='Confirme a Senha'
							value={confirmPassword}
							onChange={e => setConfirmPassword(e.target.value)}
							required
						/>
						<div className='flex items-center'>
							<input
								id='acceptTerms'
								type='checkbox'
								checked={acceptTerms}
								onChange={e => setAcceptTerms(e.target.checked)}
								className='h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded'
							/>
							<label
								htmlFor='acceptTerms'
								className='ml-2 block text-sm text-gray-600'
							>
								Concordo com os{' '}
								<NavItem
									href='/terms'
									target='_blank'
									rel='noopener noreferrer'
									className='text-blue-600 hover:text-blue-700'
								>
									termos e condições
								</NavItem>
								.
							</label>
						</div>
						<button
							type='submit'
							className={`w-full py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white ${
								isLoading ? 'bg-gray-300' : 'bg-blue-600 hover:bg-blue-700'
							} focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition duration-300`}
							disabled={isLoading}
						>
							{isLoading ? 'Registrando...' : 'Registrar'}
						</button>
					</form>
				)}
				{isSuccess && (
					<div className='mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700'>
						<p className='font-bold'>Cadastro Realizado!</p>
						<p>
							Quase lá! Por favor, verifique sua caixa de entrada ou a pasta de lixo eletrônico/spam para o e-mail de
							ativação da sua conta. É importante seguir as instruções do e-mail para concluir o processo de registro.
						</p>
						<p className='mt-4'>
							<NavItem
								href='/signin'
								className='text-blue-600 hover:text-blue-700'
							>
								Fazer Login
							</NavItem>
						</p>
					</div>
				)}
			</div>
		</div>
	);
};

export default Register;
