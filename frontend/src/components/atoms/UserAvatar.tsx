import ProfilePhoto from '../../assets/user-profile-transparent.png';

const UserAvatar = () => (
	<img
		className='h-8 w-8 rounded-full'
		src={ProfilePhoto}
		alt='Foto do usuário'
	/>
);

export default UserAvatar;
