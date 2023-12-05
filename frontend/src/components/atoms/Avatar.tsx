import React from 'react';
import userProfilePng from '../../assets/user-profile-transparent.png';

const Avatar = (): React.ReactElement => {
	return (
		<img
			src={userProfilePng}
			alt=''
			width='24'
			height='24'
			loading='lazy'
			className='inline-block align-text-top'
		/>
	);
};

export default Avatar;
