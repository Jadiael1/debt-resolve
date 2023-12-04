import { useState } from 'react';
import { useAuth } from '../../../../contexts/AuthContext';

function ResendActivationLink() {
	const [message, setMessage] = useState('');
	const [isSubmitting, setIsSubmitting] = useState(false);
	const { token } = useAuth();
	const resendActivationLink = async () => {
		setIsSubmitting(true);
		setMessage('');
		try {
			const response = await fetch('https://api.debtscrm.shop/api/v1/auth/email/resend-activation-link', {
				method: 'POST',
				headers: {
					Accept: 'application/json',
					Authorization: `Bearer ${token}`,
				},
			});

			if (!response.ok) {
				throw new Error('Falha ao reenviar o link de ativação.');
			}

			setMessage('Link de ativação reenviado com sucesso. Por favor, verifique seu e-mail.');
		} catch (error) {
			setMessage('Erro ao reenviar o link de ativação. Tente novamente mais tarde.');
		} finally {
			setIsSubmitting(false);
		}
	};

	return (
		<div className='flex flex-col items-center justify-center min-h-screen bg-gray-100 p-4'>
			<div className='bg-white shadow-lg rounded-lg p-6 w-full max-w-md'>
				<h2 className='text-2xl font-semibold text-center text-gray-800 mb-4'>Reenviar Link de Ativação</h2>
				<p className='text-center mb-6'>Clique no botão abaixo para reenviar o link de ativação da sua conta para o seu e-mail.</p>
				<button
					onClick={resendActivationLink}
					disabled={isSubmitting}
					className='w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300'
				>
					{isSubmitting ? 'Enviando...' : 'Reenviar Link'}
				</button>
				{message && <div className='mt-4 text-center text-sm font-medium text-gray-600'>{message}</div>}
			</div>
		</div>
	);
}

export default ResendActivationLink;
