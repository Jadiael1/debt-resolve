import ListItems from '../atoms/ListItems';
import NavLink from '../atoms/NavLink';
import UserDropdownReduced from './UserDropdownReduced';
import { useAuth } from '../../contexts/AuthContext';

const NavMenuReduced = () => {
	const { user } = useAuth();

	return (
		<ul className='mb-2 mr-auto flex flex-col pl-0 list-none'>
			<NavLink
				className='list-item hover:bg-white hover:bg-opacity-10'
				href='./'
			>
				Home
			</NavLink>
			<NavLink
				className='list-item hover:bg-white hover:bg-opacity-10'
				href='./'
			>
				Contato
			</NavLink>
			<ListItems className='border-t border-white my-1'></ListItems>
			{user ?
				<UserDropdownReduced />
			:	<NavLink
					className='list-item hover:bg-white hover:bg-opacity-10'
					href='/signin'
				>
					Login
				</NavLink>
			}
		</ul>
	);
};

export default NavMenuReduced;
