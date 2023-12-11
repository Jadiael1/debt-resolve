import { useRef, useState } from 'react';
import { FaTimesCircle, FaUpload } from 'react-icons/fa';
import { useAuth } from '../../contexts/AuthContext';

interface InstallmentUploadProps {
	installmentId: number;
	chargeId: number;
}

const InstallmentUpload = ({ installmentId = 0, chargeId = 0 }: InstallmentUploadProps) => {
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
		console.log(data);
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
				<div className='mt-4 flex items-center'>
					<img
						src={previewUrl}
						alt='Preview'
						className='max-w-xs w-full h-auto rounded'
					/>
					<button
						type='button'
						className='ml-2 text-red-500 hover:text-red-700'
						onClick={removeSelectedImage}
					>
						<FaTimesCircle size={24} />
					</button>
				</div>
			)}

			{previewUrl && (
				<button
					type='submit'
					form='sendProof'
					className='ml-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded flex flex-wrap items-center justify-center'
					onClick={() => handleUploadProof()}
				>
					Upload
				</button>
			)}
		</form>
	);
};

export default InstallmentUpload;
