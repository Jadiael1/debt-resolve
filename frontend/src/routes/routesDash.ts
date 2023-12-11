import { FaUserCircle } from 'react-icons/fa';
import Dash from '../components/pages/Dashboard/Home';
import CreateCharge from '../components/pages/Dashboard/Charges/create';
import IRoutes from './IRoutes';
import ListCharge from '../components/pages/Dashboard/Charges';
import ChargeDetails from '../components/pages/Dashboard/Charges/ChargeDetails';
import ChargeInvitationsList from '../components/pages/Dashboard/Charges/Invitations/list/ChargeInvitations';

const routesDash: IRoutes[] = [
	{
		path: '/dashboard',
		component: Dash,
		visibleInDisplay: false,
		displayName: 'Dashboard',
		protected: true,
		icon: FaUserCircle,
	},
	{
		path: '/dashboard/charge/create',
		component: CreateCharge,
		visibleInDisplay: false,
		displayName: 'Criar Cobrança',
		protected: true,
	},
	{
		path: '/dashboard/charges',
		component: ListCharge,
		visibleInDisplay: false,
		displayName: 'Ver Cobranças',
		protected: true,
	},
	{
		path: '/dashboard/charge/:chargeId',
		component: ChargeDetails,
		visibleInDisplay: false,
		displayName: 'Ver Cobranças',
		protected: true,
	},
	{
		path: '/dashboard/charge/charge-invitations',
		component: ChargeInvitationsList,
		visibleInDisplay: false,
		displayName: 'Ver convite para cobranças',
		protected: true,
	},
];

export default routesDash;
