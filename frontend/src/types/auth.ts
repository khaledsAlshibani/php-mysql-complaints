export interface LoginFormData {
    username: string;
    password: string;
}

export interface RegisterFormData extends LoginFormData {
    firstName: string;
    lastName: string;
    birthDate: string;
    confirmPassword: string;
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

export interface UserAuth {
    id: User['id'],
    username: User['username'],
    firstName: User['firstName'],
    lastName: User['lastName'],
    role: User['role'],
}

export interface AuthResponse {
    status: 'success' | 'error';
    data?: UserAuth;
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

export interface AuthContextType {
    isLoading: boolean;
    setUser: (user: UserAuth | null) => void;
}