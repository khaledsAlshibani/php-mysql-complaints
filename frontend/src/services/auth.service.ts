import type { AuthResponse, LoginFormData, RegisterData } from '@/types/auth';
import axiosInstance from '@/lib/axios';

export const authService = () => {
	const login = async (data: LoginFormData): Promise<AuthResponse> => {
		try {
			const response = await axiosInstance.post('/auth/login', data);
			return response.data;
		} catch (error: any) {
			if (error.response?.data) {
				return error.response.data;
			}

			return {
				status: 'error',
				error: {
					message: error.message || 'An unknown error occurred',
					code: 500,
					errorCode: 'SERVER_ERROR'
				}
			};
		}
	};

	const register = async (registerData: RegisterData): Promise<AuthResponse> => {
		try {
			const { role, confirm_password, ...requestData } = registerData as any;
			const response = await axiosInstance.post('/auth/register', requestData);
			return response.data;
		} catch (error: any) {
			if (error.response?.data) {
				return error.response.data;
			}

			return {
				status: 'error',
				error: {
					message: error.message || 'An unknown error occurred',
					code: 500,
					errorCode: 'SERVER_ERROR'
				}
			};
		}
	};

	const logout = async (): Promise<void> => {
		await axiosInstance.post('/auth/logout');
	};

	return {
		login,
		register,
		logout
	};
};
