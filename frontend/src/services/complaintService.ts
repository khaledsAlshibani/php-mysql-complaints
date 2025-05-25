import { isAxiosError } from 'axios';
import axiosInstance from '@/lib/axios';
import type {
  Complaint,
  CreateComplaintRequest,
  UpdateComplaintRequest,
  GetComplaintResponse,
  GetComplaintsResponse,
  CreateComplaintResponse,
  UpdateComplaintResponse,
  DeleteComplaintResponse,
  GetAllComplaintsParams
} from '@/types/api/complaint';

export const complaintService = {
  getAll: async (params?: GetAllComplaintsParams): Promise<GetComplaintsResponse> => {
    try {
      const searchParams = new URLSearchParams();
      if (params?.status) searchParams.append('status', params.status);
      if (params?.search) searchParams.append('search', params.search);
      const queryString = searchParams.toString();
      const response = await axiosInstance.get(`/complaints${queryString ? `?${queryString}` : ''}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch complaints');
      }
      throw error;
    }
  },

  getById: async (id: number): Promise<GetComplaintResponse> => {
    try {
      const response = await axiosInstance.get(`/complaints/${id}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Complaint not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch complaint');
      }
      throw error;
    }
  },

  create: async (data: CreateComplaintRequest): Promise<CreateComplaintResponse> => {
    try {
      const response = await axiosInstance.post('/complaints', data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 422) {
          throw new Error('Validation failed');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to create complaint');
      }
      throw error;
    }
  },

  update: async (id: number, data: UpdateComplaintRequest): Promise<UpdateComplaintResponse> => {
    try {
      const response = await axiosInstance.put(`/complaints/${id}`, data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Complaint not found');
        }
        if (error.response?.status === 422) {
          throw new Error('Validation failed');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to update complaint');
      }
      throw error;
    }
  },

  delete: async (id: number): Promise<DeleteComplaintResponse> => {
    try {
      const response = await axiosInstance.delete(`/complaints/${id}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Complaint not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to delete complaint');
      }
      throw error;
    }
  },

  getAllAdmin: async (params?: { search?: string }): Promise<GetComplaintsResponse> => {
    try {
      const searchParams = new URLSearchParams();
      if (params?.search) searchParams.append('search', params.search);
      const queryString = searchParams.toString();
      const response = await axiosInstance.get(`/complaints/admin/all${queryString ? `?${queryString}` : ''}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch complaints');
      }
      throw error;
    }
  },

  getByStatus: async (status: Complaint['status'], params?: { search?: string }): Promise<GetComplaintsResponse> => {
    try {
      const searchParams = new URLSearchParams();
      if (params?.search) searchParams.append('search', params.search);
      const queryString = searchParams.toString();
      const response = await axiosInstance.get(`/complaints/admin/status/${status}${queryString ? `?${queryString}` : ''}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch complaints by status');
      }
      throw error;
    }
  },

  updateStatus: async (id: number, status: Complaint['status']): Promise<UpdateComplaintResponse> => {
    try {
      const response = await axiosInstance.patch(`/complaints/${id}/status`, { status });
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        if (error.response?.status === 404) {
          throw new Error('Complaint not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to update complaint status');
      }
      throw error;
    }
  }
};