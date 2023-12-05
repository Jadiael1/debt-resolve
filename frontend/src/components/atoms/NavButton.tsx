import Button from './Button';

interface NavButtonProps {
	handleClickMenu: () => void;
}

const NavButton = ({ handleClickMenu }: NavButtonProps) => {
	return (
		<Button
			className='ml-1 sm:hidden border-white border cursor-pointer py-1 px-2 text-lg leading-4 bg-white bg-opacity-10 bg-transparent rounded transition-all ease-in-out duration-300'
			aria-label='Menu'
			onClick={handleClickMenu}
		>
			<svg
				className='h-6 w-6'
				viewBox='0 0 20 20'
				fill='white'
				xmlns='http://www.w3.org/2000/svg'
			>
				<title>Mobile menu</title>
				<path d='M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z'></path>
			</svg>
		</Button>
	);
};

export default NavButton;
