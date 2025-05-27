export interface PartialResponseUser {
  id: number;
  username: string;
  fullName: string;
}

export interface ApiSuccessResponse<T> {
  status: 'success';
  data: T;
  message: string;
}

// Common error codes shared across the application
export type CommonErrorCode =
  | 'AUTHENTICATION_REQUIRED'
  | 'INVALID_PAYLOAD'
  | 'VALIDATION_ERROR'
  | 'UNAUTHORIZED_ACCESS'
  | 'ACCESS_DENIED';

export interface ApiErrorResponse<E extends string = string> {
  status: 'error';
  error: {
    message: string;
    code: number;
    details?: Record<string, unknown>;
    errorCode?: E | CommonErrorCode;
  };
}

export type ApiResponse<T, E extends string = string> = ApiSuccessResponse<T> | ApiErrorResponse<E>;