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
/*
npm install --save-dev eslint @typescript-eslint/parser @typescript-eslint/eslint-plugin eslint-plugin-react eslint-plugin-react-hooks eslint-config-prettier eslint-plugin-prettier
#vite
npm install --save-dev eslint-plugin-react eslint-config-prettier eslint-plugin-prettier
*/
