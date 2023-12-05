import React, { useState, useContext, useEffect } from 'react';
import { AuthContext } from '../../../contexts/AuthContext';
import MailIcon from '../../../components/atoms/icons/MailIcon';
import LockClosedIcon from '../../../components/atoms/icons/LockClosedIcon';
import { useNavigate } from 'react-router-dom';
import bglogin from '../../../assets/bglogin.png';

const Login = () => {
	const [email, setEmail] = useState('');
	const [password, setPassword] = useState('');
	const [loginError, setLoginError] = useState(false);
	const [isLogin, setIsLogin] = useState(false);
	const { login, user } = useContext(AuthContext);
	const navigate = useNavigate();

	useEffect(() => {
		if (user) {
			navigate('/dashboard');
		}
	}, [user]);

	useEffect(() => {
		document.title = 'DebtsResolve - Login';
	}, []);

	const handleSubmit = async (event: React.FormEvent) => {
		event.preventDefault();
		setIsLogin(true);
		setLoginError(false);
		try {
			await login(email, password);
			navigate('/dashboard');
		} catch (error) {
			setLoginError(true);
		} finally {
			setIsLogin(false);
		}
	};

	const handleBack = () => {
		navigate('/');
	};

	return (
		<div
			className='flex items-center justify-center min-h-screen bg-fixed bg-no-repeat bg-cover bg-center'
			style={{ backgroundImage: `url(${bglogin})` }}
		>
			<div className='w-full max-w-sm md:max-w-md lg:max-w-lg bg-white bg-opacity-95 rounded-xl shadow-xl p-6 border border-gray-300 sm:p-8'>
				<button
					onClick={handleBack}
					className='text-blue-600 hover:text-blue-700 text-sm font-medium mb-4'
				>
					← Voltar
				</button>
				<h2 className='text-3xl font-bold text-gray-800 text-center mb-4'>Acesse o DebtsResolve</h2>
				<p className='text-center text-sm font-medium text-gray-500 mb-6'>Inicie sua jornada financeira</p>
				{loginError && (
					<div
						className='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'
						role='alert'
					>
						<p className='font-bold'>Falha no Login</p>
						<p>Usuário ou senha incorretos. Por favor, tente novamente.</p>
					</div>
				)}
				<form
					onSubmit={handleSubmit}
					className='space-y-4'
				>
					<div className='relative'>
						<MailIcon className='absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-blue-500 z-20' />
						<input
							type='email'
							id='email'
							className='block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent'
							placeholder='E-mail'
							value={email}
							onChange={e => setEmail(e.target.value)}
							required
						/>
					</div>
					<div className='relative'>
						<LockClosedIcon className='absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-blue-500 z-20' />
						<input
							type='password'
							id='password'
							className='block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent'
							placeholder='Senha'
							value={password}
							onChange={e => setPassword(e.target.value)}
							required
						/>
					</div>
					<button
						type='submit'
						disabled={isLogin}
						className='w-full py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition duration-300'
					>
						Entrar
					</button>
				</form>
				<div className='flex justify-between mt-6 text-sm font-medium'>
					<a
						href='/forgot-password'
						className='text-blue-600 hover:text-blue-700'
					>
						Esqueceu sua senha?
					</a>
					<a
						href='/signup'
						className='text-blue-600 hover:text-blue-700'
					>
						Criar uma nova conta
					</a>
				</div>
			</div>
		</div>
	);
};

export default Login;