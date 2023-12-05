import React from 'react';
import debtscrmLogo from '../../assets/debtscrm1.png';
import { Anchor } from './Anchor';

const Logo = (): React.ReactElement => {
	return (
		<Anchor
			href='#'
			className='flex items-center mr-4 text-sm no-underline whitespace-nowrap text-white'
		>
			<img
				src={debtscrmLogo}
				alt=''
				width='30'
				height='24'
				loading='lazy'
				className='inline-block align-text-top'
			/>
			<span className='font-semibold'>DebtsCRM</span>
		</Anchor>
	);
};

export default Logo;
