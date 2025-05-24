import type { AuthResponse, LoginFormData, RegisterFormData } from '@/types/api/auth';
import axiosInstance from '@/lib/axios';
import { isAxiosError } from 'axios';

export const authService = {
	login: async (credentials: LoginFormData): Promise<AuthResponse> => {
		console.log('Attempting to login with credentials:', credentials);
		try {
			const response = await axiosInstance.post('/auth/login', credentials);
			console.log('Login successful:', response.data);
			return response.data;
		} catch (error) {
			console.error('Login error:', error);
			if (isAxiosError(error)) {
				if (error.response?.status === 401) {
					throw new Error('Invalid username or password');
				}
				throw new Error(error.response?.data?.message || 'Login failed');
			}
			throw error;
		}
	},

	register: async (credentials: RegisterFormData): Promise<AuthResponse> => {
		try {
			// Transform the data to match backend's expected format
			const transformedData = {
				username: credentials.username,
				password: credentials.password,
				first_name: credentials.firstName,
				last_name: credentials.lastName,
				birth_date: credentials.birthDate
			};

			const response = await axiosInstance.post('/auth/register', transformedData);
			return response.data;
		} catch (error) {
			if (isAxiosError(error)) {
				if (error.response?.status === 400) {
					throw new Error('Username already exists');
				}
				throw new Error(error.response?.data?.message || 'Registration failed');
			}
			throw error;
		}
	},

	logout: async (): Promise<AuthResponse> => {
		try {
			const response = await axiosInstance.post('/auth/logout');
			return response.data;
		} catch (error) {
			if (isAxiosError(error)) {
				throw new Error(error.response?.data?.message || 'logout failed');
			}
			throw error;
		}
	},

	refresh: async (): Promise<AuthResponse> => {
		try {
			const response = await axiosInstance.post('/auth/refresh');
			return response.data;
		} catch (error) {
			if (isAxiosError(error)) {
				throw new Error(error.response?.data?.message || 'refresh failed');
			}
			throw error;
		}
	},
};
