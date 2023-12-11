import { FaMoneyBillWave } from 'react-icons/fa';

const LoadingComponent = () => {
	return (
		<div className='flex justify-center items-center h-screen bg-gradient-to-r from-gray-400 to-gray-500'>
			<div className='p-8 rounded-lg shadow-lg bg-gray-300 bg-opacity-75 backdrop-filter backdrop-blur-lg'>
				<FaMoneyBillWave className='animate-bounce text-green-600 h-12 w-12 mx-auto' />
				<h2 className='text-lg font-semibold text-gray-800 mt-4 text-center'>Carregando...</h2>
				<p className='text-gray-500'>Aguarde enquanto processamos sua solicitação.</p>
			</div>
		</div>
	);
};

export default LoadingComponent;

// type TLoadingComponent = {
// 	loading: boolean;
// };

// import { useState, useEffect } from 'react';

// const LoadingBar = ({ loading }: TLoadingComponent) => {
// 	const [progress, setProgress] = useState(0);

// 	useEffect(() => {
// 		let interval = 0;

// 		if (loading) {
// 			setProgress(20);
// 			interval = setInterval(() => {
// 				setProgress(oldProgress => {
// 					const diff = Math.random() * 40;
// 					return Math.min(oldProgress + diff, 95);
// 				});
// 			}, 100);
// 		}

// 		return () => {
// 			if (interval) {
// 				clearInterval(interval);
// 				setProgress(100); // Completa imediatamente quando o loading termina
// 			}
// 		};
// 	}, [loading]);

// 	return (
// 		<div className='fixed top-0 left-0 right-0 z-50'>
// 			<div
// 				style={{ width: `${progress}%` }}
// 				className='h-2 bg-blue-500 transition-all duration-300 ease-linear'
// 			></div>
// 		</div>
// 	);
// };

// const Skeleton = () => {
// 	return (
// 		<div className='animate-pulse'>
// 			<div className='bg-gray-300 h-20 rounded mb-4'></div>
// 			<div className='space-y-4'>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 				<div className='h-4 bg-gray-300 rounded'></div>
// 			</div>
// 		</div>
// 	);
// };

// const LoadingComponent = ({ loading }: TLoadingComponent) => {
// 	return (
// 		<>
// 			<LoadingBar loading={loading} />
// 			<div className={`relative ${loading ? 'blur-sm' : ''}`}>{loading && <Skeleton />}</div>
// 		</>
// 	);
// };

// export default LoadingComponent;
