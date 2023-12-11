import { useEffect, useState } from 'react';
import { FaTimes } from 'react-icons/fa';

type TNotificationProps = {
	message: string;
	type: string;
	onClose: () => void;
};

const Notification = ({ message, type, onClose }: TNotificationProps) => {
	const [progress, setProgress] = useState(100);

	useEffect(() => {
		const interval = setInterval(() => {
			setProgress(prev => {
				if (prev > 0) return prev - 25;
				clearInterval(interval);
				return 0;
			});
		}, 1700);

		return () => clearInterval(interval);
	}, []);

	useEffect(() => {
		if (progress === 0) {
			onClose();
		}
	}, [progress, onClose]);

	const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

	return (
		<div
			className={`${bgColor} text-white p-4 rounded flex items-center justify-between fixed top-16 left-0 right-0 z-50 w-full max-w-2xl mx-auto`}
		>
			<div className='flex-grow'>
				<p>{message}</p>
				<div className='h-1 bg-white rounded-full overflow-hidden'>
					<div
						className='h-full bg-black'
						style={{ width: `${progress}%` }}
					/>
				</div>
			</div>
			<button onClick={onClose}>
				<FaTimes />
			</button>
		</div>
	);
};

export default Notification;
