import HomeList from '../components/pages/Home/list';
import IRoutes from './IRoutes';

const routeHome: IRoutes[] = [
	{
		path: '/',
		component: HomeList,
		visibleInDisplay: true,
		displayName: 'Home',
		protected: false,
	},
];

export default routeHome;
