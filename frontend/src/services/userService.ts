import type { User } from '@/types/api/auth';
import axiosInstance from '@/lib/axios';
import type { ServiceResponse, UpdateProfileData, UpdatePasswordData, DeleteAccountData } from '@/types/api/user';

export const userService = () => {
  const getProfile = async (): Promise<ServiceResponse<User>> => {
    try {
      const response = await axiosInstance.get('/users/me');
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

  const updateProfile = async (data: UpdateProfileData): Promise<ServiceResponse<User>> => {
    try {
      const response = await axiosInstance.put('/users/me', data);
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

  const updatePhoto = async (file: File): Promise<ServiceResponse<{ photoPath: string }>> => {
    try {
      const formData = new FormData();
      formData.append('photo', file);

      const response = await axiosInstance.post('/users/photo', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
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

  const updatePassword = async (data: UpdatePasswordData): Promise<ServiceResponse> => {
    try {
      const response = await axiosInstance.put('/users/password', data);
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

  const deleteAccount = async (data: DeleteAccountData): Promise<ServiceResponse> => {
    try {
      const response = await axiosInstance.delete('/users/me', { data });
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

  return {
    getProfile,
    updateProfile,
    updatePhoto,
    updatePassword,
    deleteAccount
  };
};
