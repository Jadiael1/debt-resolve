import React from 'react';
import IconButton from '../atoms/IconButton';
import { FaBars, FaSignInAlt, FaTimes } from 'react-icons/fa';
import { useAuth } from '../../contexts/AuthContext';

interface MobileControlButtonsProps {
	isMenuOpen: boolean;
	toggleMenu: () => void;
	navigateToSignIn: () => void;
}

const MobileControlButtons: React.FC<MobileControlButtonsProps> = ({ isMenuOpen, toggleMenu, navigateToSignIn }) => {
	const { user, isLoading } = useAuth();
	return (
		<div className='md:hidden flex items-center'>
			{!isLoading && !user && (
				<IconButton
					merge={true}
					onClick={navigateToSignIn}
					icon={
						<div className='mr-4 px-1 rounded border-2 flex hover:bg-gray-500'>
							<span>Entrar</span>
							<FaSignInAlt className='h-6 w-6 ml-2' />
						</div>
					}
					defaultClassName={true}
					aria-label='Entrar'
				/>
			)}
			<IconButton
				onClick={toggleMenu}
				icon={isMenuOpen ? <FaTimes className='h-6 w-6' /> : <FaBars className='h-6 w-6' />}
			/>
		</div>
	);
};

export default MobileControlButtons;
