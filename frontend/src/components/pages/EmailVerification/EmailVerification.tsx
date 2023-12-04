import { useState, useEffect } from 'react';
import { useParams, useLocation } from 'react-router-dom';
import { Link } from 'react-router-dom';
import debtsresolveactivation from '../../../assets/debtsresolveactivation.png';

function EmailVerification() {
	const { userId, token } = useParams();
	const location = useLocation();
	const [verificationStatus, setVerificationStatus] = useState('');

	useEffect(() => {
		document.title = 'DebtsResolve - Verificação de e-mail';
	}, []);

	useEffect(() => {
		const queryParams = new URLSearchParams(location.search);
		const expires = queryParams.get('expires');
		const signature = queryParams.get('signature');

		const verifyEmail = async () => {
			try {
				const response = await fetch(`https://api.debtscrm.shop/api/v1/auth/email/verify/${userId}/${token}?expires=${expires}&signature=${signature}`, {
					method: 'GET',
					headers: {
						Accept: 'application/json',
					},
				});

				if (response.ok) {
					const data = await response.json();
					if (data.status === 'success') {
						setVerificationStatus('success');
					} else {
						setVerificationStatus('error');
					}
				} else {
					setVerificationStatus('error');
				}
			} catch (error) {
				console.error('Erro na verificação:', error);
				setVerificationStatus('error');
			}
		};

		verifyEmail();
	}, [userId, token, location]);

	const renderStatusBox = () => {
		switch (verificationStatus) {
			case '':
				return <p className='text-center text-lg text-gray-800 font-semibold animate-pulse'>Verificando...</p>;
			case 'success':
				return (
					<div className='text-center p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-lg rounded transform transition-all duration-500 scale-105'>
						<p className='font-bold text-xl mb-2 animate-bounce'>Parabéns!</p>
						<p className='mb-4'>Sua conta foi ativada com sucesso. Agora você pode acessar todos os recursos do nosso site.</p>
						<Link
							to='/signin'
							className='inline-block bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-110'
						>
							Fazer Login
						</Link>
					</div>
				);
			case 'error':
				return (
					<div className='text-center p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-lg rounded transform transition-all duration-500 scale-105'>
						<p className='font-bold text-xl'>Erro na Ativação</p>
						<p className='mb-4'>
							Houve um problema ao ativar sua conta. Por favor,{' '}
							<Link
								to='/signin'
								className='text-blue-600 hover:text-blue-800'
							>
								faça login
							</Link>{' '}
							e solicite um novo link de ativação por e-mail.
						</p>
					</div>
				);
			default:
				return null;
		}
	};

	return (
		<div
			className='flex items-center justify-center min-h-screen bg-fixed bg-no-repeat bg-cover bg-center'
			style={{ backgroundImage: `url(${debtsresolveactivation})` }}
		>
			<div className='w-full max-w-md p-6 bg-white bg-opacity-95 rounded-xl shadow-2xl border border-gray-300'>{renderStatusBox()}</div>
		</div>
	);
}

export default EmailVerification;
