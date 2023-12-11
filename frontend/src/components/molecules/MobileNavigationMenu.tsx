import routes from '../../routes/routes';
import NavItem from '../atoms/NavItem';

const MobileNavigationMenu = () => {
	return (
		<div className='hidden md:flex'>
			{routes
				.filter(route => route.visibleInDisplay === true)
				.map(route => (
					<NavItem
						key={route.path}
						href={route.path}
						icon={() => (route.icon ? <route.icon /> : <></>)}
						className={`flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-300 hover:bg-gray-700 ${
							location.pathname === route.path ? 'text-white opacity-100' : 'text-white opacity-50'
						}`}
					>
						{route.displayName}
					</NavItem>
				))}
		</div>
	);
};

export default MobileNavigationMenu;
