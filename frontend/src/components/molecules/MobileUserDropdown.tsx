import React from 'react';
import NavItem from '../atoms/NavItem';
import { FaUserCircle, FaSignOutAlt } from 'react-icons/fa';

interface MobileUserDropdownProps {
	isDropdownOpen: boolean;
}

const MobileUserDropdown: React.FC<MobileUserDropdownProps> = ({ isDropdownOpen }) => {
	if (!isDropdownOpen) return null;
	return (
		<div className={`${isDropdownOpen ? 'block' : 'hidden'} text-center bg-gray-800`}>
			<NavItem
				href='/dashboard'
				icon={() => <FaUserCircle className='mr-2' />}
				className='px-3 py-2 rounded-md text-sm text-white hover:bg-gray-700 select-none flex items-center justify-center'
			>
				Dashboard
			</NavItem>
			<NavItem
				href='/signout'
				icon={() => <FaSignOutAlt className='mr-2' />}
				className='px-3 py-2 rounded-md text-sm text-white hover:bg-gray-700 select-none flex items-center justify-center'
			>
				Sair
			</NavItem>
		</div>
	);
};

export default MobileUserDropdown;
