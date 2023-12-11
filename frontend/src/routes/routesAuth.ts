import { FaSignInAlt } from 'react-icons/fa';
import Login from '../components/pages/Login';
import ResetPassword from '../components/pages/ResetPassword/';
import Register from '../components/pages/Register';
import ForgotPassword from '../components/pages/ForgotPassword';
import EmailVerification from '../components/pages/EmailVerification';
import ResendActivationLink from '../components/pages/Dashboard/ResendActivationLink';
import IRoutes from './IRoutes';

const routesAuth: IRoutes[] = [
	{
		path: '/signup',
		component: Register,
		visibleInDisplay: false,
		displayName: 'Registrar',
		protected: false,
	},
	{
		path: '/verify-email/:userId/:token',
		component: EmailVerification,
		visibleInDisplay: false,
		displayName: 'Verificação de E-mail',
		protected: false,
	},
	{
		path: '/signin',
		component: Login,
		visibleInDisplay: false,
		displayName: 'Entrar',
		protected: false,
		icon: FaSignInAlt,
	},
	{
		path: '/dashboard/resend-activation-link',
		component: ResendActivationLink,
		visibleInDisplay: false,
		displayName: 'Reenviar link de ativação',
		protected: true,
	},
	{
		path: '/forgot-password',
		component: ForgotPassword,
		visibleInDisplay: false,
		displayName: 'Solicitação de redefinição de senha',
		protected: false,
	},
	{
		path: '/reset-password',
		component: ResetPassword,
		visibleInDisplay: false,
		displayName: 'Redefinir Senha',
		protected: false,
	},
];

export default routesAuth;
