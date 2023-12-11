import React from 'react';
import UserAvatar from '../atoms/UserAvatar';
import DropdownArrow from '../atoms/DropdownArrow';

interface UserDropdownProps {
	userName: string;
	isDropdownOpen: boolean;
	toggleDropdown: () => void;
}

const UserDropdown: React.FC<UserDropdownProps> = ({ userName, isDropdownOpen, toggleDropdown }) => (
	<div
		className='flex items-center cursor-pointer'
		onClick={toggleDropdown}
	>
		<UserAvatar />
		<span className={`ml-2 font-semibold text-lg ${isDropdownOpen ? 'opacity-90' : 'opacity-80'} hover:opacity-90`}>
			{userName}
		</span>
		<DropdownArrow isOpen={isDropdownOpen} />
	</div>
);

export default UserDropdown;
