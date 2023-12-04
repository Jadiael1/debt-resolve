import Login from '../components/pages/Login/Login';
import IRoutes from './IRoutes';

const routeLogin: IRoutes[] = [
	{
		path: '/signin',
		component: Login,
		visibleInDisplay: true,
		displayName: 'Entrar',
		protected: false,
	},
];

export default routeLogin;
