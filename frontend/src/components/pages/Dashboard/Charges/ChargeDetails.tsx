import { useState, useEffect, FormEvent } from 'react';
import { useParams } from 'react-router-dom';
import { FaBan, FaCheck } from 'react-icons/fa';
import { useAuth } from '../../../../contexts/AuthContext';
import Sidebar from '../../../organisms/Sidebar';
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
	awaiting_approval: boolean | number;
	user_id: number | null;
	charge_id: number;
};

const ChargeDetails = () => {
	const { chargeId } = useParams<{ chargeId: string }>();
	const [charge, setCharge] = useState<Charge | null>(null);
	const [installments, setInstallments] = useState<Installment[]>([]);
	const [loading, setLoading] = useState(true);
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

	// Functions to handle upload proof, request approval, accept payment, etc.

	const handleConfirmPayment = async (installmentId: number, accept: boolean) => {
		const myHeaders = new Headers();
		myHeaders.append('Authorization', `Bearer ${token}`);
		myHeaders.append('Content-Type', 'application/json');
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
		console.log(data);
	};

	const handleInviteUser = async (email: string) => {
		const response = await fetch('https://api.debtscrm.shop/api/v1/charge-invitations/invitations', {
			method: 'POST',
			headers: {
				Authorization: `Bearer ${token}`,
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({ email, charge_id: chargeId }),
		});
		const data = await response.json();
		console.log(data);
	};

	const sendInstallmentPaymentForApproval = async (installmentId: number) => {
		const request = await fetch(`https://api.debtscrm.shop/api/v1/installments/send-payment/${installmentId}`, {
			method: 'POST',
			headers: {
				Authorization: `Bearer ${token}`,
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({ charge_id: charge?.id }),
		});
		const response = await request.json();
		console.log(response);
	};

	const isOnlyParticipant =
		charge &&
		((charge.collector_id === user?.id && !charge.debtor_id) ||
			(charge.debtor_id === user?.id && !charge.collector_id));

	const oppositeRole = charge && charge.collector_id === parseInt(user?.id as string) ? 'Devedor' : 'Cobrador';

	return (
		<Sidebar>
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
							className='ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded'
						>
							Enviar Convite
						</button>
					</form>
				</div>
			:	<div className='container mx-auto p-4'>
					<h1 className='text-2xl font-semibold mb-4'>Detalhes da Cobrança</h1>
					{loading ?
						<div>Carregando...</div>
					:	<>
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
												className='mb-4 p-4 border rounded flex justify-between flex-wrap'
											>
												<div className='block break-all'>
													<p>
														Parcela {installment.installment_number}: R$ {installment.value}
													</p>
													<p>Vencimento: {installment.due_date}</p>
												</div>
												<div>
													{installment.payment_proof && (
														<p>
															<img
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
														/>
													:	<button
															type='button'
															className='bg-green-700 hover:bg-green-500 text-white font-bold px-2 rounded flex items-center justify-center flex-wrap text-sm'
															onClick={() => {
																sendInstallmentPaymentForApproval(installment.id);
															}}
														>
															Enviar Pagamento Para Analise
														</button>)}
											</div>
										))}
								</div>
							</div>
							{/* List installments */}

							{/* If user is debtor, show upload button */}
							{/* If user is collector, show payments awaiting approval */}
						</>
					}
				</div>
			}
		</Sidebar>
	);
};

export default ChargeDetails;
