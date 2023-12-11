import { useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import { FaAngleDown, FaAngleUp, FaSignOutAlt, FaUserCircle } from 'react-icons/fa';
import ProfilePhoto from '../../assets/user-profile-transparent.png';
import Logo from '../atoms/Logo';
import NavItem from '../atoms/NavItem';
import NavigationMenu from '../molecules/NavigationMenu';
import UserDropdown from '../molecules/UserDropdown';
import LoginButtonWithIcon from '../molecules/LoginButtonWithIcon';
import MobileUserDropdown from '../molecules/MobileUserDropdown';
import MobileControlButtons from '../molecules/MobileControlButtons';
import UserDropdownSkeleton from '../atoms/UserDropdownSkeleton';

const Navbar = () => {
	const [isMenuOpen, setIsMenuOpen] = useState(true);
	const [isUserDropdownOpen, setIsUserDropdownOpen] = useState(false);
	const [isMobileUserDropdownOpen, setIsMobileUserDropdownOpen] = useState(true);

	const navigate = useNavigate();
	const { user, isLoading } = useAuth();
	const dropdownRef = useRef<HTMLDivElement>(null);
	const mobileDropdownRef = useRef<HTMLDivElement>(null);

	useEffect(() => {
		const handleResize = () => {
			if (window.innerWidth > 768) {
				// setIsMenuOpen(false);
				setIsUserDropdownOpen(false);
			}
		};
		const handleKeyDown = (event: KeyboardEvent) => {
			if (event.key === 'Escape') {
				setIsUserDropdownOpen(false);
			}
		};
		const handleClickOutside = (event: MouseEvent) => {
			if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
				setIsUserDropdownOpen(false);
			}
		};
		window.addEventListener('resize', handleResize);
		document.addEventListener('keydown', handleKeyDown);
		document.addEventListener('mousedown', handleClickOutside);
		return () => {
			window.removeEventListener('resize', handleResize);
			document.removeEventListener('keydown', handleKeyDown);
			document.removeEventListener('mousedown', handleClickOutside);
		};
	}, []);

	return (
		<nav className='bg-gray-800 text-white shadow-lg'>
			<div className='flex justify-between items-center py-4 sm:px-6'>
				<div className='flex items-center'>
					<Logo />
					<NavigationMenu defaultClassName />
				</div>

				{user && !isLoading ?
					<div
						className='hidden md:flex relative'
						ref={dropdownRef}
					>
						<UserDropdown
							userName={user.name as string}
							isDropdownOpen={isUserDropdownOpen}
							toggleDropdown={() => setIsUserDropdownOpen(!isUserDropdownOpen)}
						/>
						<div
							className={`${
								isUserDropdownOpen ? 'block' : 'hidden'
							} absolute right-0 top-full mt-2 w-48 rounded-md shadow-lg py-1 bg-gray-800 ring-1 ring-black ring-opacity-5 z-50 transition-all duration-200 ease-out`}
						>
							<NavItem
								href='/dashboard'
								icon={() => <FaUserCircle />}
								className='flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-300 hover:bg-gray-700'
							>
								Dashboard
							</NavItem>
							<hr className='my-1 border-gray-700' />
							<NavItem
								href='/signout'
								icon={() => <FaSignOutAlt />}
								className='flex items-center px-3 py-2 rounded-md text-sm font-medium transition duration-300 hover:bg-gray-700'
							>
								Sair
							</NavItem>
						</div>
					</div>
				: isLoading ?
					<UserDropdownSkeleton />
				:	<LoginButtonWithIcon
						onClick={() => {
							navigate('/signin');
						}}
					/>
				}
				<MobileControlButtons
					isMenuOpen={isMenuOpen}
					toggleMenu={() => setIsMenuOpen(!isMenuOpen)}
					navigateToSignIn={() => navigate('/signin')}
				/>
			</div>
			<div className={`${isMenuOpen ? 'block' : 'hidden'} md:hidden text-center bg-gray-800`}>
				<div className='text-center'>
					{user && !isLoading ?
						<div
							className='py-2 border-b border-gray-700'
							ref={mobileDropdownRef}
						>
							<div
								className='flex items-center justify-center py-2 cursor-pointer select-none'
								onClick={() => setIsMobileUserDropdownOpen(!isMobileUserDropdownOpen)}
							>
								<img
									className='h-8 w-8 rounded-full select-none'
									src={ProfilePhoto}
									alt='Foto do usuário'
								/>
								<span className='ml-2 font-semibold text-lg text-white select-none'>{user.name}</span>
								{isMobileUserDropdownOpen ?
									<FaAngleUp className='ml-1 h-4 w-4 text-white transition duration-300' />
								:	<FaAngleDown className='ml-1 h-4 w-4 text-white transition duration-300' />}
							</div>
							<MobileUserDropdown isDropdownOpen={isMobileUserDropdownOpen} />
						</div>
					:	isLoading && <>Loading...</>}
					<NavigationMenu />
				</div>
			</div>
		</nav>
	);
};

export default Navbar;
