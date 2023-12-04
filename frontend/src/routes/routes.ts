import routeHome from './routeHome';
import routeLogin from './routeLogin';
import routeRegister from './routeRegister';
import routeEmailVerification from './routeEmailVerification';
import routesDash from './routesDash';

const routes = [...routeHome, ...routeLogin, ...routeRegister, ...routeEmailVerification, ...routesDash];

export default routes;
