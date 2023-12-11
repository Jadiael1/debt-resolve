import { useEffect, useState } from 'react';
import { RiMailOpenLine } from 'react-icons/ri'; // Exemplo de ícone
import { useAuth } from '../../../../../../contexts/AuthContext';
import Notification from '../../../../../atoms/Notification';
import Sidebar from '../../../../../organisms/Sidebar';

type TInvitations = {
	id: number;
	email: string;
	token: string;
	charge_id: number;
	user_id: number;
	is_valid: number;
	created_at: string;
	updated_at: string;
};

const ChargeInvitationsList = () => {
	const { token, user } = useAuth();
	const [invitations, setInvitations] = useState<TInvitations[]>([]);
	const [notification, setNotification] = useState({ message: '', type: '' });

	const showNotification = (message: string, type: string) => {
		setNotification({ message, type });
	};

	const clearNotification = () => {
		setNotification({ message: '', type: '' });
	};

	useEffect(() => {
		const getInvitationsByEmail = async () => {
			try {
				const response = await fetch(
					`https://api.debtscrm.shop/api/v1/charge-invitations/${encodeURIComponent(user?.email as string)}/email`,
					{
						method: 'GET',
						headers: {
							Accept: 'application/json',
							Authorization: `Bearer ${token}`,
						},
					},
				);
				const data = await response.json();
				if (
					response.ok &&
					response.status === 200 &&
					data.message === 'Invitations successfully found by email' &&
					data.data['charge-invitation'].length > 0
				) {
					setInvitations(data.data['charge-invitation']);
				}
			} catch (error) {
				// showNotification('Erro ao carregar convites', 'error');
			}
		};
		getInvitationsByEmail();
	}, [token, user?.email]);

	const handleInvitationAction = async (invitationToken: string, accept: boolean) => {
		try {
			const myHeaders = new Headers();
			myHeaders.append('Authorization', `Bearer ${token}`);
			myHeaders.append('Content-Type', 'application/json');
			const response = await fetch(
				`https://api.debtscrm.shop/api/v1/charge-invitations/process-charge-invitations/${invitationToken}`,
				{
					method: 'POST',
					headers: myHeaders,
					body: JSON.stringify({
						accept,
					}),
					redirect: 'follow',
				},
			);
			const data = await response.json();
			if (
				response.ok &&
				response.status === 200 &&
				data.message === 'Congratulations now you participate in this charge'
			) {
				showNotification('Convite aceito com sucesso', 'success');
				setInvitations(prevInvitations => prevInvitations.filter(invitation => invitation.token !== invitationToken));
			}

			if (
				response.ok &&
				response.status === 200 &&
				data.message === 'You have successfully declined the billing invitation'
			) {
				showNotification('Convite recusado com sucesso', 'success');
				setInvitations(prevInvitations => prevInvitations.filter(invitation => invitation.token !== invitationToken));
			}

			if (response.status === 410 && data.message === 'This invite link has expired or does not exist') {
				showNotification('Este link de convite expirou ou não existe', 'error');
			}

			if (
				response.status === 410 &&
				data.message === 'This invitation link has already been used and is no longer valid'
			) {
				showNotification('Este link de convite já foi usado e não é mais válido', 'error');
			}

			if (
				response.status === 409 &&
				data.message === 'It is not possible to participate in this charge, there is already a collector and the debtor'
			) {
				showNotification('Não é possível participar dessa cobrança, já existe cobrador e devedor', 'error');
			}

			if (
				response.status === 422 &&
				data.message === 'You cannot play the role of collector and debtor at the same time in a charge'
			) {
				showNotification(
					'Você não pode desempenhar o papel de cobrador e devedor ao mesmo tempo em uma cobrança',
					'error',
				);
			}
		} catch (error) {
			showNotification('Erro desconhecido ao ir para cobrança', 'error');
		}
	};

	return (
		<Sidebar>
			<div className='container mx-auto p-4 mt-4'>
				{notification.message && (
					<Notification
						message={notification.message}
						type={notification.type}
						onClose={clearNotification}
					/>
				)}
				<h2 className='text-lg font-bold mb-4'>Convites para Cobranças</h2>
				<div className='grid grid-cols-1 md:grid-cols-2 gap-4'>
					{invitations.map(invitation => (
						<div
							key={invitation.id}
							className='p-4 border rounded-lg block md:flex md:items-center md:justify-between'
						>
							<div className='flex items-center'>
								<RiMailOpenLine className='text-xl mr-2' />
								<p className='text-sm'>Cobrança #{invitation.charge_id}</p>
							</div>

							<div className=''>
								<button
									className='mr-2 mb-2 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'
									onClick={() => handleInvitationAction(invitation.token, false)}
								>
									Recusar
								</button>
								<button
									className='bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded'
									onClick={() => handleInvitationAction(invitation.token, true)}
								>
									Aceitar
								</button>
							</div>
						</div>
					))}
				</div>
			</div>
		</Sidebar>
	);
};

export default ChargeInvitationsList;
