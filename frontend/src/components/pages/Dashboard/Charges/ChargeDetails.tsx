import { useState, useEffect, FormEvent } from 'react';
import { useParams } from 'react-router-dom';
import { FaBan, FaCheck, FaEnvelope, FaSpinner } from 'react-icons/fa';
import { useAuth } from '../../../../contexts/AuthContext';
import Sidebar from '../../../organisms/Sidebar';
import Notification from '../../../atoms/Notification';
import InstallmentUpload from '../../../organisms/InstallmentUpload';

type Charge = {
	id: number;
	name: string;
	description: string;
	amount: string;
	amount_paid: string;
	payment_information: string | null;
	installments_number: number;
	due_day: number;
	collector_id: number | null;
	debtor_id: number | null;
	created_at: string;
	updated_at: string;
};

type payment_proof = {
	originalFileName: string;
	newFileName: string;
	path: string;
};

type Installment = {
	id: number;
	value: string;
	installment_number: number;
	due_date: string;
	amount_paid: string | null;
	paid: boolean;
	payment_proof: payment_proof | null;
	awaiting_approval: boolean;
	user_id: number | null;
	charge_id: number;
};

const ChargeDetails = () => {
	const { chargeId } = useParams<{ chargeId: string }>();
	const [charge, setCharge] = useState<Charge | null>(null);
	const [installments, setInstallments] = useState<Installment[]>([]);
	const [loading, setLoading] = useState(true);
	const [loadingInvite, setLoadingInvite] = useState(false);
	const [messageInvited, setMessageInvited] = useState<{ message: string; type: 'success' | 'error' | '' }>({
		message: '',
		type: '',
	});
	const [notification, setNotification] = useState({ message: '', type: '' });
	const { token, user } = useAuth();
	// const navigate = useNavigate();

	useEffect(() => {
		const fetchChargeDetails = async () => {
			const chargeResponse = await fetch(`https://api.debtscrm.shop/api/v1/charges/${chargeId}/charge`, {
				headers: { Authorization: `Bearer ${token}` },
			});
			const chargeData = await chargeResponse.json();
			if (chargeData.status === 'success') {
				setCharge(chargeData.data.charge);
			}

			const installmentsResponse = await fetch(`https://api.debtscrm.shop/api/v1/installments/charge/${chargeId}`, {
				headers: { Authorization: `Bearer ${token}` },
			});
			const installmentsData = await installmentsResponse.json();
			if (installmentsData.status === 'success') {
				setInstallments(installmentsData.data.Installments);
			}

			setLoading(false);
		};
		fetchChargeDetails();
	}, [chargeId, token]);

	const showNotification = (message: string, type: string) => {
		setNotification({ message, type });
	};

	const clearNotification = () => {
		setNotification({ message: '', type: '' });
	};

	const handleConfirmPayment = async (installmentId: number, accept: boolean) => {
		const myHeaders = new Headers();
		myHeaders.append('Authorization', `Bearer ${token}`);
		myHeaders.append('Content-Type', 'application/json');
		try {
			const response = await fetch(
				`https://api.debtscrm.shop/api/v1/installments/${installmentId}/charge/${chargeId}/accept-payment-approval-by-collector`,
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
			if (response.ok && response.status === 200 && data.message === 'Payment marked as paid successfully') {
				showNotification('Pagamento de parcela confirmado com sucesso!', 'success');
				setInstallments(prevInstallments =>
					prevInstallments.map(installment =>
						installment.id === installmentId ?
							{ ...installment, paid: true, user_id: user?.id as number }
						:	installment,
					),
				);
			}
			if (response.ok && response.status === 200 && data.message === 'Payment successfully declined') {
				showNotification('Pagamento de parcela recusado com sucesso!', 'error');
				setInstallments(prevInstallments =>
					prevInstallments.map(installment =>
						installment.id === installmentId ? { ...installment, awaiting_approval: false } : installment,
					),
				);
			}
		} catch (error) {
			showNotification('Erro inesperado.', 'error');
		}
	};

	const handleInviteUser = async (email: string) => {
		setLoadingInvite(true);
		try {
			const response = await fetch('https://api.debtscrm.shop/api/v1/charge-invitations/invitations', {
				method: 'POST',
				headers: {
					Authorization: `Bearer ${token}`,
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({ email, charge_id: chargeId }),
			});
			const data = await response.json();
			if (response.status === 400 && data.message === 'You already participate in this charge') {
				showNotification('Você não pode convidar a sí mesmo para uma cobrança', 'error');
				setMessageInvited({
					message: 'Você não pode convidar a sí mesmo para uma cobrança',
					type: 'error',
				});
			}
			if (
				response.status === 429 &&
				data.message ===
					'Invitation limit for this email and billing exceeded. Wait a week before sending a new invitation'
			) {
				showNotification(
					'Limite de convites para este e-mail excedido. Espere uma semana antes de enviar um novo convite',
					'error',
				);
				setMessageInvited({
					message: 'Limite de convites para este e-mail excedido. Espere uma semana antes de enviar um novo convite',
					type: 'error',
				});
			}
			if (response.status === 500 && data.message === 'Unexpected error when creating resource') {
				showNotification('Erro inesperado ao criar recurso', 'error');
				setMessageInvited({
					message: 'Erro inesperado ao criar recurso',
					type: 'error',
				});
			}
			if (
				response.ok &&
				response.status === 201 &&
				data.message === 'Invitation to register and participate in billing sent successfully'
			) {
				showNotification(
					'Convite enviado com sucesso, aguarde até que o participante aceite este convite. Volte novamente mais tarde.',
					'success',
				);
				setMessageInvited({
					message:
						'Convite enviado com sucesso, aguarde até que o participante aceite este convite. Volte novamente mais tarde.',
					type: 'success',
				});
			}
		} catch (error) {
			showNotification('Erro inesperado.', 'error');
		}
		setLoadingInvite(false);
	};

	const sendInstallmentPaymentForApproval = async (installmentId: number) => {
		try {
			const response = await fetch(`https://api.debtscrm.shop/api/v1/installments/send-payment/${installmentId}`, {
				method: 'POST',
				headers: {
					Authorization: `Bearer ${token}`,
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({ charge_id: charge?.id }),
			});
			const data = await response.json();
			if (
				response.ok &&
				response.status === 200 &&
				data.message === 'Payment of the installment of the charge sent for analysis successfully'
			) {
				showNotification('Pagamento da parcela enviado para análise com sucesso', 'success');
				setInstallments(prevInstallments =>
					prevInstallments.map(installment =>
						installment.id === installmentId ? { ...installment, awaiting_approval: true } : installment,
					),
				);
			}
			if (response.status === 409 && data.message === 'This installment is already under payment approval analysis') {
				showNotification('Esta parcela já está em análise de aprovação de pagamento', 'error');
			}
			if (
				response.status === 422 &&
				data.message === 'Before sending the payment for analysis you need to send proof of payment'
			) {
				showNotification(
					'Antes de enviar o pagamento para análise é necessário enviar o comprovante de pagamento',
					'error',
				);
			}
			if (
				response.status === 403 &&
				data.message === 'You cannot send payment for an installment of a charge for which you are not the debtor'
			) {
				showNotification(
					'Você não pode enviar o pagamento de uma parcela de uma cobrança do qual você não é o devedor',
					'error',
				);
			}
		} catch (error) {
			showNotification('Erro inesperado.', 'error');
		}
	};

	const isOnlyParticipant =
		charge &&
		((charge.collector_id === user?.id && !charge.debtor_id) ||
			(charge.debtor_id === user?.id && !charge.collector_id));

	const oppositeRole = charge && charge.collector_id === parseInt(user?.id as string) ? 'Devedor' : 'Cobrador';
	const myRole = charge && charge.collector_id === parseInt(user?.id as string) ? 'Cobrador' : 'Devedor';

	return (
		<Sidebar>
			{notification.message && (
				<Notification
					message={notification.message}
					type={notification.type}
					onClose={clearNotification}
				/>
			)}
			{isOnlyParticipant ?
				<div className='mb-6 p-4 border rounded'>
					<h3 className='text-lg font-semibold mb-2 text-center'>Convidar Participante</h3>
					<p className='mb-2 text-center'>
						Para que essa cobrança funcione, você precisa convidar um <b>{oppositeRole}</b> para está cobrança.
					</p>
					<form
						className='text-center'
						onSubmit={(evt: FormEvent<HTMLFormElement>) => {
							evt.preventDefault();
							const emailInput = evt.currentTarget.elements.namedItem('email') as HTMLInputElement;
							const email = emailInput.value;
							handleInviteUser(email);
						}}
					>
						<input
							type='email'
							name='email'
							required
							placeholder='Email do destinatário'
							className='border p-2 rounded'
						/>
						<button
							type='submit'
							className={`${
								loadingInvite ? 'bg-gray-500' : 'bg-blue-500 hover:bg-blue-700'
							} ml-2 text-white font-bold py-1 px-2 rounded`}
							disabled={loadingInvite ? true : false}
						>
							<span className='flex items-center'>
								Enviar Convite
								<FaEnvelope className='ml-2' />
								{loadingInvite && <FaSpinner className='animate-spin ml-2' />}
							</span>
						</button>
					</form>
					{messageInvited.message && (
						<p
							className={`mb-2 text-center mt-2 ${
								messageInvited.type === 'success' ? 'bg-green-500' : 'bg-yellow-500'
							}  rounded text-white font-medium`}
						>
							{messageInvited.message}
						</p>
					)}
				</div>
			:	<div className='container mx-auto p-4'>
					<h1 className='text-2xl font-semibold mb-4'>Detalhes da Cobrança</h1>
					{loading ?
						<div>Carregando...</div>
					:	<>
							<div className='flex justify-between flex-wrap'>
								<div className='mb-6'>
									<h2 className='text-xl font-bold mb-2'>Informações da Cobrança</h2>
									<p>
										<strong>Nome:</strong> {charge?.name}
									</p>
									<p>
										<strong>Descrição:</strong> {charge?.description}
									</p>
									<p>
										<strong>Valor Total:</strong> R$ {charge?.amount}
									</p>
									<p>
										<strong>Debito Restante:</strong> R$ {charge?.amount_paid}
									</p>
								</div>

								<div className='mb-6'>
									<p>
										<strong>Criação da cobrança: </strong> {(charge?.created_at as string).substring(0, 19)}
									</p>
									<p>
										<strong>Minha função: </strong> {myRole}
									</p>
								</div>
							</div>

							{user?.id === charge?.collector_id &&
								installments
									.filter(inst => inst.awaiting_approval && !inst.paid)
									.map(installment => (
										<div key={installment.id}>
											<h2 className='text-xl font-bold mb-2'>Parcelas para dar baixa</h2>
											<div className='mb-4 p-4 border rounded flex justify-between flex-wrap'>
												<div className='block break-all'>
													<p>
														Parcela {installment.installment_number}: R$ {installment.value}
													</p>
													<p>Vencimento: {installment.due_date}</p>
												</div>
												<div>
													{installment.payment_proof && (
														<p>
															<a
																href={`${installment.payment_proof.path}`}
																target='_blank'
																rel='noreferrer'
															>
																<img
																	src={`${installment.payment_proof.path}`}
																	alt='comprovante'
																	width={'128px'}
																/>
															</a>
														</p>
													)}
												</div>
												<div className='flex items-center'>
													<button
														onClick={() => handleConfirmPayment(installment.id, true)}
														className='ml-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded flex items-center'
													>
														<FaCheck className='mr-2' />
														Confirmar
													</button>
													<button
														onClick={() => handleConfirmPayment(installment.id, false)}
														className='ml-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded flex items-center'
													>
														<FaBan className='mr-2' />
														Recusar
													</button>
												</div>
											</div>
										</div>
									))}

							<div>
								<h2 className='text-xl font-bold mb-2'>Parcelas</h2>
								<div>
									{installments
										.filter(installment => !installment.paid && !installment.awaiting_approval)
										.map(installment => (
											<div
												key={installment.id}
												className='mb-4 p-4 border rounded md:flex md:justify-between md:flex-wrap'
											>
												<div className='block break-all text-center'>
													<p>
														Parcela {installment.installment_number}: R$ {installment.value}
													</p>
													<p>Vencimento: {installment.due_date}</p>
												</div>
												<div className='mb-2 mt-2 flex justify-center items-center'>
													{installment.payment_proof && (
														<p>
															<img
																className=''
																src={`${installment.payment_proof.path}`}
																alt='comprovante'
																width={'128px'}
															/>
														</p>
													)}
												</div>
												{user?.id === charge?.debtor_id &&
													(!installment.payment_proof ?
														<InstallmentUpload
															installmentId={installment.id}
															chargeId={charge?.id as number}
															showNotification={showNotification}
															setInstallments={setInstallments}
														/>
													:	<div className='flex items-center justify-center'>
															<button
																type='button'
																className='xs:bg-green-700 xs:hover:bg-green-500 xs:text-white xs:font-bold xs:p-2 xs:rounded xs:text-sm text-xs px-0 py-1 bg-green-700 hover:bg-green-500 rounded text-white'
																onClick={() => {
																	sendInstallmentPaymentForApproval(installment.id);
																}}
															>
																<span>Enviar Pagamento Para Analise</span>
															</button>
														</div>)}
											</div>
										))}
								</div>
							</div>

							<div>
								<h2 className='text-xl font-bold mb-2 text-green-400'>Parcelas Pagas</h2>
								<div>
									{installments
										.filter(installment => installment.paid && installment.awaiting_approval)
										.map(installment => (
											<div
												key={installment.id}
												className='mb-4 p-4 border border-green-400 border-2 rounded md:flex md:justify-between md:flex-wrap'
											>
												<div className='block break-all text-center'>
													<p>
														Parcela {installment.installment_number}: R$ {installment.value}
													</p>
													<p>Vencimento: {installment.due_date}</p>
												</div>
												<div className='mb-2 mt-2 flex justify-center items-center'>
													{installment.payment_proof && (
														<p>
															<img
																className=''
																src={`${installment.payment_proof.path}`}
																alt='comprovante'
																width={'128px'}
															/>
														</p>
													)}
												</div>
											</div>
										))}
								</div>
							</div>
						</>
					}
				</div>
			}
		</Sidebar>
	);
};

export default ChargeDetails;
