import routeHome from './routeHome';
import routeLogin from './routeLogin';
import routeRegister from './routeRegister';
import routeEmailVerification from './routeEmailVerification';

const routes = [...routeHome, ...routeLogin, ...routeRegister, ...routeEmailVerification];

export default routes;
