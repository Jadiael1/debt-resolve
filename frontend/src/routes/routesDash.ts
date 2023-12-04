import Dash from '../components/pages/Dashboard/Home';
import ResendActivationLink from '../components/pages/Dashboard/ResendActivationLink';
import IRoutes from './IRoutes';

const routesDash: IRoutes[] = [
	{
		path: '/dashboard',
		component: Dash,
		visibleInDisplay: true,
		displayName: 'Dashboard',
		protected: true,
	},
	{
		path: '/dashboard/resend-activation-link',
		component: ResendActivationLink,
		visibleInDisplay: false,
		displayName: 'Reenviar link de ativação',
		protected: true,
	},
];

export default routesDash;
