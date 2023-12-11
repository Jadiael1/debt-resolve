import { useNavigate } from 'react-router-dom';
import debtscrmLogo from '../../assets/debtscrm1.png';

const Logo = () => {
	const navigate = useNavigate();
	return (
		<a
			onClick={(evt: React.MouseEvent<HTMLAnchorElement>) => {
				evt.preventDefault();
				navigate('/');
			}}
			href='/'
			className='flex items-center text-white mr-6 ml-2 sm:ml-0'
		>
			<img
				src={debtscrmLogo}
				alt='DebtsCRM Logo'
				className='mr-2 h-6 sm:h-9'
			/>
			<span className='self-center font-semibold whitespace-nowrap text-sm sm:text-lg'>DebtsCRM</span>
		</a>
	);
};

export default Logo;
