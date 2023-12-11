import { FaHome, FaInfoCircle } from 'react-icons/fa';
import HomeList from '../components/pages/Home';
import AboutPage from '../components/pages/About';
import IRoutes from './IRoutes';

const routesSite: IRoutes[] = [
	{
		path: '/',
		component: HomeList,
		visibleInDisplay: true,
		displayName: 'Home',
		protected: false,
		icon: FaHome,
	},
	{
		path: '/about',
		component: AboutPage,
		visibleInDisplay: true,
		displayName: 'Sobre',
		protected: false,
		icon: FaInfoCircle,
	},
];

export default routesSite;
