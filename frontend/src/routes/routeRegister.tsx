import Register from '../components/pages/Register/Register';
import IRoutes from './IRoutes';

const routeRegister: IRoutes[] = [
	{
		path: '/signup',
		component: Register,
		visibleInDisplay: false,
		displayName: 'Registrar',
		protected: false,
	},
];

export default routeRegister;
