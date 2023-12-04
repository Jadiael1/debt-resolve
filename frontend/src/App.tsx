import React from 'react';
import AppRoutes from './routes';
import './assets/main.css';
import { AuthProvider } from './contexts/AuthContext';

const App: React.FC = () => {
	return (
		<AuthProvider>
			<AppRoutes />
		</AuthProvider>
	);
};

export default App;
// https://api.debtscrm.shop/api/v1
// XSS
