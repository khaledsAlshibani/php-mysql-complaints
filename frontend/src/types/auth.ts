export interface LoginFormData {
    username: string;
    password: string;
}

export interface SignupFormData extends RegisterData {
    confirm_password: string;
}

export interface User {
    id: number;
    username: string;
    firstName: string;
    lastName: string;
    birthDate?: string;
    role: string;
}

export interface AuthResponse {
    status: 'success' | 'error';
    data?: User;
    message?: string;
    error?: {
        message: string;
        code: number;
        details?: Array<{ field: string; issue: string }>;
        errorCode?: string;
    };
}

export interface RegisterData {
    username: string;
    password: string;
    first_name: string;
    last_name?: string;
    birth_date: string;
}
