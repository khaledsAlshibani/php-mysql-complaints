import { API_URL } from '@/constants/api';
import type { AuthResponse, LoginFormData, RegisterData } from '@/types/auth';

export class AuthService {
    static async login(data: LoginFormData): Promise<AuthResponse> {
        try {
            const response: Response = await fetch(`${API_URL}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'include',
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch {
                    errorData = null;
                }

                return {
                    status: 'error',
                    error: {
                        message: errorData?.message || `Server error: ${response.statusText}`,
                        code: response.status,
                        errorCode: 'SERVER_ERROR'
                    }
                };
            }

            const responseData: AuthResponse = await response.json();
            return responseData;
        } catch (error: unknown) {
            const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
            
            return {
                status: 'error',
                error: {
                    message: `Failed to connect to server: ${errorMessage}`,
                    code: 500,
                    errorCode: 'SERVER_ERROR'
                }
            };
        }
    }

    static async register(registerData: RegisterData): Promise<AuthResponse> {
        try {
            const response: Response = await fetch(`${API_URL}/auth/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'include',
                body: JSON.stringify(registerData),
            });

            if (!response.ok) {
                let errorData;
                try {
                    errorData = await response.json();
                } catch {
                    errorData = null;
                }

                return {
                    status: 'error',
                    error: {
                        message: errorData?.message || `Server error: ${response.statusText}`,
                        code: response.status,
                        errorCode: 'SERVER_ERROR'
                    }
                };
            }

            const responseData: AuthResponse = await response.json();
            return responseData;
        } catch (error: unknown) {
            const errorMessage = error instanceof Error ? error.message : 'Unknown error occurred';
            
            return {
                status: 'error',
                error: {
                    message: `Failed to connect to server: ${errorMessage}`,
                    code: 500,
                    errorCode: 'SERVER_ERROR'
                }
            };
        }
    }
}
