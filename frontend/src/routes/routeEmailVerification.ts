import EmailVerification from '../components/pages/EmailVerification/EmailVerification';
import IRoutes from './IRoutes';

const routeEmailVerification: IRoutes[] = [
	{
		path: '/verify-email/:userId/:token',
		component: EmailVerification,
		visibleInDisplay: false,
		displayName: 'Verificação de E-mail',
		protected: false,
	},
];

export default routeEmailVerification;
