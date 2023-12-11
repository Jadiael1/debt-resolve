import { BrowserRouter, Routes, Route } from 'react-router-dom';
import routes from './routes';
import { ProtectedRoute } from './ProtectedRoute';
import { useAuth } from '../contexts/AuthContext';
import LoadingComponent from '../components/pages/Loading';

function AppRoutes() {
	const { isLoading } = useAuth();
	return (
		<BrowserRouter>
			<Routes>
				{routes.map(({ path, component: Component, protected: isProtected }) => {
					return (
						<Route
							key={path}
							path={path}
							element={
								isProtected ?
									<ProtectedRoute path={path}>
										<Component />
									</ProtectedRoute>
								: !isLoading ?
									<Component />
								:	<LoadingComponent />
							}
						/>
					);
				})}
			</Routes>
		</BrowserRouter>
	);
}

export default AppRoutes;
