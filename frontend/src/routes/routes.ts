import routeHome from './routeHome';
import routeLogin from './routeLogin';
import routeRegister from './routeRegister';
import routeEmailVerification from './routeEmailVerification';
import routesDash from './routesDash';
import routeForgotPassword from './routeForgotPassword';
import routeResetPassword from './routeResetPassword';

const routes = [
	...routeHome,
	...routeLogin,
	...routeRegister,
	...routeEmailVerification,
	...routesDash,
	...routeForgotPassword,
	...routeResetPassword,
];

export default routes;
