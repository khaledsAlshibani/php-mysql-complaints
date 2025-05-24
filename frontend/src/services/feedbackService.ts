import { isAxiosError } from 'axios';
import axiosInstance from '@/lib/axios';
import type {
  CreateFeedbackRequest,
  UpdateFeedbackRequest,
  GetFeedbackResponse,
  GetFeedbacksResponse,
  CreateFeedbackResponse,
  UpdateFeedbackResponse,
  DeleteFeedbackResponse
} from '@/types/api/feedback';

export const feedbackService = {
  getAllForComplaint: async (complaintId: number): Promise<GetFeedbacksResponse> => {
    try {
      const response = await axiosInstance.get(`/complaints/${complaintId}/feedback`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Complaint not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch feedback for complaint');
      }
      throw error;
    }
  },

  getAllForSuggestion: async (suggestionId: number): Promise<GetFeedbacksResponse> => {
    try {
      const response = await axiosInstance.get(`/suggestions/${suggestionId}/feedback`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Suggestion not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch feedback for suggestion');
      }
      throw error;
    }
  },

  getById: async (id: number): Promise<GetFeedbackResponse> => {
    try {
      const response = await axiosInstance.get(`/feedback/${id}`);
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

  create: async (data: CreateFeedbackRequest): Promise<CreateFeedbackResponse> => {
    try {
      const response = await axiosInstance.post('/feedback', data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
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

  update: async (id: number, data: UpdateFeedbackRequest): Promise<UpdateFeedbackResponse> => {
    try {
      const response = await axiosInstance.put(`/feedback/${id}`, data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
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

  delete: async (id: number): Promise<DeleteFeedbackResponse> => {
    try {
      const response = await axiosInstance.delete(`/feedback/${id}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Feedback not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to delete feedback');
      }
      throw error;
    }
  },

  // Admin-specific endpoints
  getAllByAdmin: async (): Promise<GetFeedbacksResponse> => {
    try {
      const response = await axiosInstance.get('/feedback/admin');
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch all feedback');
      }
      throw error;
    }
  }
};