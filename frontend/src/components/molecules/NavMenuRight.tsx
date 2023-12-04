import Avatar from '../atoms/Avatar';
import Button from '../atoms/Button';
import ListItems from '../atoms/ListItems';
import UserDropdown from './UserDropdown';
import { useAuth } from '../../contexts/AuthContext';
import { Anchor } from '../atoms/Anchor';

const NavMenuRight = () => {
	const { user } = useAuth();
	return user ? (
		<ul className='flex flex-row ml-auto list-none items-center space-x-4'>
			<ListItems className='relative group'>
				<div className='flex space-x-2 hover:bg-white hover:bg-opacity-10 hover:rounded transition-all ease-in-out duration-300 cursor-pointer'>
					<span className='ml-1'>User</span>
					<Avatar />
				</div>
				<UserDropdown />
			</ListItems>
		</ul>
	) : (
		<Anchor href='/signin'>
			<Button className='hover:border-b hover:border-blue-700 text-white font-semibold px-4 rounded transition duration-300 ease-in-out shadow hover:shadow-lg'>Login</Button>
		</Anchor>
	);
};

export default NavMenuRight;
