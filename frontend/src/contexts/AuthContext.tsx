import { useState, createContext, useContext, ReactNode, useEffect } from 'react';

export type UserType = {
	id: string | number;
	role?: string;
	username: string;
	email: string;
	name?: string;
	isEmailVerified: boolean;
};

export type LoginResponse = {
	status: string;
	message: string;
	data: {
		user: UserType;
		token: string;
		tokenType: string;
	};
};

type AuthContextType = {
	user: UserType | null;
	token: string | null;
	login: (email: string, password: string) => Promise<void>;
	logout: () => void;
	isLoading: boolean;
};

export const AuthContext = createContext<AuthContextType>({
	user: null,
	token: null,
	login: async () => {},
	logout: () => {},
	isLoading: true,
});

export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
	const [user, setUser] = useState<UserType | null>(null);
	const [token, setToken] = useState<string | null>(localStorage.getItem('token'));
	const [isLoading, setIsLoading] = useState(true);

	useEffect(() => {
		const loadUser = async () => {
			if (token) {
				try {
					const request = await fetch('https://api.debtscrm.shop/api/v1/user', {
						method: 'GET',
						headers: {
							Authorization: `Bearer ${token}`,
						},
					});
					const data: LoginResponse = await request.json();
					if (data.status === 'success') {
						setUser(data.data.user);
						setToken(token);
					} else {
						setUser(null);
						setToken(null);
					}
				} catch (error) {
					setUser(null);
					setToken(null);
				}
			}
			setIsLoading(false);
		};

		loadUser();
	}, [token]);

	const login = async (email: string, password: string) => {
		try {
			const request = await fetch('https://api.debtscrm.shop/api/v1/auth/signin', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Accept: 'application/json',
				},
				body: JSON.stringify({ email, password }),
			});
			const data: LoginResponse = await request.json();
			if (data.status === 'success') {
				setUser(data.data.user);
				setToken(data.data.token);
				localStorage.setItem('token', data.data.token);
			} else {
				throw new Error(data.message);
			}
		} catch (error) {
			if (error instanceof Error && error.message === 'Validation error') {
				throw new Error(error.message);
			}
			throw new Error(`unknown error`);
		}
	};

	const logout = () => {
		setUser(null);
		setToken(null);
		localStorage.removeItem('token');
	};

	return <AuthContext.Provider value={{ user, token, login, logout, isLoading }}>{children}</AuthContext.Provider>;
};
