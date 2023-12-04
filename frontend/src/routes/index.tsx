import { BrowserRouter, Routes, Route } from 'react-router-dom';
import routes from './routes';
import { ProtectedRoute } from './ProtectedRoute';

function AppRoutes() {
	return (
		<BrowserRouter>
			<Routes>
				{routes.map(({ path, component: Component, protected: isProtected }) => {
					return (
						<Route
							key={path}
							path={path}
							element={
								isProtected ? (
									<ProtectedRoute>
										<Component />
									</ProtectedRoute>
								) : (
									<Component />
								)
							}
						/>
					);
				})}
			</Routes>
		</BrowserRouter>
	);
}

export default AppRoutes;
