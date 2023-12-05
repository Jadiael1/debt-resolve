import React from 'react';

const Header: React.FC = () => {
	return (
		<header className='bg-white py-12'>
			<div className='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center'>
				<h1 className='text-3xl leading-9 font-bold text-gray-900'>Bem-vindo ao DebtsResolve</h1>
				<p className='mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl'>
					Gerencie suas dívidas com eficiência e facilidade.
				</p>
			</div>
		</header>
	);
};

export default Header;
