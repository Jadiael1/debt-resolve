import NavLink from '../atoms/NavLink';

const NavMenuLeft = () => (
	<ul className='flex flex-row mr-auto list-none space-x-4'>
		<NavLink href='./'>Home</NavLink>
		<NavLink href='./'>Contato</NavLink>
	</ul>
);

export default NavMenuLeft;
