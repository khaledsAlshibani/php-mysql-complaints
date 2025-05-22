export interface LoginFormData {
    username: string;
    password: string;
}

export interface RegisterData extends LoginFormData {
    firstName: string;
    lastName?: string;
    birthDate?: string;
    confirm_password: string;
}

export interface User {
    id: number;
    username: string;
    firstName: string;
    lastName: string | null;
    birthDate: string | null;
    photoPath: string | null;
    role: UserRole;
    createdAt: string | null;
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

export enum UserRole {
    ADMIN = 'admin',
    USER = 'user',
}

