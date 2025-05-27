export interface UpdatePasswordData {
  currentPassword: string;
  newPassword: string;
}

export interface UpdateProfileData {
  firstName: string;
  lastName: string;
  birthDate: string;
}

export interface DeleteAccountData {
  password: string;
}

export interface ServiceResponse<T = unknown> {
  status: 'success' | 'error';
  data?: T;
  message?: string;
  error?: {
    message: string;
    code: number;
    details?: Array<{ field: string; issue: string }>;
    errorCode?: string;
  };
}
