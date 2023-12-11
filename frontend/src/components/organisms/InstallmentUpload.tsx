import { useRef, useState } from 'react';
import { FaTimesCircle, FaUpload } from 'react-icons/fa';
import { useAuth } from '../../contexts/AuthContext';

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

interface InstallmentUploadProps {
	installmentId: number;
	chargeId: number;
	showNotification?: (message: string, type: string) => void;
	setInstallments?: React.Dispatch<React.SetStateAction<Installment[]>>;
}

const InstallmentUpload = ({
	installmentId = 0,
	chargeId = 0,
	showNotification,
	setInstallments,
}: InstallmentUploadProps) => {
	const [selectedImage, setSelectedImage] = useState<File | null>(null);
	const [previewUrl, setPreviewUrl] = useState<string | null>(null);
	const fileInputRef = useRef<HTMLInputElement>(null);
	const { token } = useAuth();

	const handleUploadProof = async () => {
		const formData = new FormData();
		formData.append('image', selectedImage as File);
		formData.append('charge_id', `${chargeId}`);
		const response = await fetch(`https://api.debtscrm.shop/api/v1/installments/upload-receipt/${installmentId}`, {
			method: 'POST',
			headers: { Authorization: `Bearer ${token}` },
			body: formData,
		});
		const data = await response.json();
		if (
			response.ok &&
			response.status === 200 &&
			data.message === 'Proof sent successfully' &&
			showNotification &&
			setInstallments
		) {
			showNotification('Comprovante enviado com sucesso', 'success');
			setInstallments(prevInstallments =>
				prevInstallments.map(installment =>
					installment.id === installmentId ?
						{ ...installment, payment_proof: { originalFileName: '', newFileName: '', path: data.data.path } }
					:	installment,
				),
			);
		}
		if (
			response.status === 400 &&
			data.message === 'This installment is already under payment approval analysis' &&
			showNotification
		) {
			showNotification('Esta parcela já está em análise de aprovação de pagamento', 'error');
		}
		if (
			response.status === 403 &&
			data.message === 'You cannot send payment for an installment of a charge for which you are not the debtor' &&
			showNotification
		) {
			showNotification(
				'Você não pode enviar o pagamento de uma parcela de um encargo do qual você não é o devedor',
				'error',
			);
		}
	};

	const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
		const file = e.target.files ? e.target.files[0] : null;
		if (file) {
			setSelectedImage(file);
			setPreviewUrl(URL.createObjectURL(file));
		}
	};

	const removeSelectedImage = () => {
		setSelectedImage(null);
		setPreviewUrl(null);
		if (fileInputRef.current) {
			fileInputRef.current.value = '';
		}
	};

	return (
		<form
			method='POST'
			encType='multipart/form-data'
			onSubmit={(e: React.FormEvent<HTMLFormElement>) => {
				e.preventDefault();
			}}
			className='flex items-center justify-center'
		>
			<input
				type='file'
				name='image'
				required
				hidden
				ref={fileInputRef}
				onChange={handleImageChange}
			/>
			{!previewUrl && (
				<button
					type='button'
					className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded flex items-center justify-center flex-wrap'
					onClick={() => fileInputRef.current?.click()}
				>
					<FaUpload className='mr-2 text-sm' />
					Escolha uma imagem
				</button>
			)}

			{previewUrl && (
				<div className='mt-4 relative'>
					<img
						src={previewUrl}
						alt='Preview'
						className='max-w-xs w-full h-auto rounded'
					/>
					<button
						type='button'
						className='absolute top-1 right-1'
						onClick={removeSelectedImage}
					>
						<FaTimesCircle
							className='fill-red-500 rounded border-2 border-red-500 hover:fill-red-400 bg-slate-300 hover:bg-slate-100'
							size={24}
						/>
					</button>
					<button
						type='submit'
						form='sendProof'
						className='bg-green-500 mt-2 text-center hover:bg-green-700 text-white font-bold py-1 px-2 rounded w-full'
						onClick={() => handleUploadProof()}
					>
						Upload
					</button>
				</div>
			)}
		</form>
	);
};

export default InstallmentUpload;
