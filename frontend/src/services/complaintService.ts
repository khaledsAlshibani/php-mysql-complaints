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
  GetAllComplaintsParams,
  CreateFeedbackRequest,
  UpdateFeedbackRequest,
  GetFeedbackResponse,
  GetAllFeedbackResponse,
  CreateFeedbackResponse,
  UpdateFeedbackResponse,
  DeleteFeedbackResponse
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
  },

  // Feedback methods
  getAllFeedback: async (complaintId: number): Promise<GetAllFeedbackResponse> => {
    try {
      const response = await axiosInstance.get(`/complaints/${complaintId}/feedback`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Complaint not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch feedback');
      }
      throw error;
    }
  },

  getFeedbackById: async (complaintId: number, feedbackId: number): Promise<GetFeedbackResponse> => {
    try {
      const response = await axiosInstance.get(`/complaints/${complaintId}/feedback/${feedbackId}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Feedback not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch feedback');
      }
      throw error;
    }
  },

  createFeedback: async (complaintId: number, data: CreateFeedbackRequest): Promise<CreateFeedbackResponse> => {
    try {
      const response = await axiosInstance.post(`/complaints/${complaintId}/feedback`, data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        if (error.response?.status === 404) {
          throw new Error('Complaint not found');
        }
        if (error.response?.status === 422) {
          throw new Error('Validation failed');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to create feedback');
      }
      throw error;
    }
  },

  updateFeedback: async (complaintId: number, feedbackId: number, data: UpdateFeedbackRequest): Promise<UpdateFeedbackResponse> => {
    try {
      const response = await axiosInstance.put(`/complaints/${complaintId}/feedback/${feedbackId}`, data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        if (error.response?.status === 404) {
          throw new Error('Feedback not found');
        }
        if (error.response?.status === 422) {
          throw new Error('Validation failed');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to update feedback');
      }
      throw error;
    }
  },

  deleteFeedback: async (complaintId: number, feedbackId: number): Promise<DeleteFeedbackResponse> => {
    try {
      const response = await axiosInstance.delete(`/complaints/${complaintId}/feedback/${feedbackId}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        if (error.response?.status === 404) {
          throw new Error('Feedback not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to delete feedback');
      }
      throw error;
    }
  }
};