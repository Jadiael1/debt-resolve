import ForgotPassword from '../components/pages/ForgotPassword';
import IRoutes from './IRoutes';

const routeForgotPassword: IRoutes[] = [
	{
		path: '/forgot-password',
		component: ForgotPassword,
		visibleInDisplay: false,
		displayName: 'Solicitação de redefinição de senha',
		protected: false,
	},
];

export default routeForgotPassword;
