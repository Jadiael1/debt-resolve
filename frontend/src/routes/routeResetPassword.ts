import ResetPassword from '../components/pages/ResetPassword/';
import IRoutes from './IRoutes';

const routeResetPassword: IRoutes[] = [
	{
		path: '/reset-password',
		component: ResetPassword,
		visibleInDisplay: false,
		displayName: 'Redefinir Senha',
		protected: false,
	},
];

export default routeResetPassword;
