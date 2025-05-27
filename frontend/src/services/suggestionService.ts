import { isAxiosError } from 'axios';
import axiosInstance from '@/lib/axios';
import type {
  Suggestion,
  CreateSuggestionRequest,
  UpdateSuggestionRequest,
  GetSuggestionResponse,
  GetSuggestionsResponse,
  CreateSuggestionResponse,
  UpdateSuggestionResponse,
  DeleteSuggestionResponse,
  GetAllSuggestionsParams,
  CreateFeedbackRequest,
  UpdateFeedbackRequest,
  GetFeedbackResponse,
  GetAllFeedbackResponse,
  CreateFeedbackResponse,
  UpdateFeedbackResponse,
  DeleteFeedbackResponse
} from '@/types/api/suggestion';

export const suggestionService = {
  getAll: async (params?: GetAllSuggestionsParams): Promise<GetSuggestionsResponse> => {
    try {
      const searchParams = new URLSearchParams();
      if (params?.status) searchParams.append('status', params.status);
      if (params?.search) searchParams.append('search', params.search);
      const queryString = searchParams.toString();
      const response = await axiosInstance.get(`/suggestions${queryString ? `?${queryString}` : ''}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch suggestions');
      }
      throw error;
    }
  },

  getById: async (id: number): Promise<GetSuggestionResponse> => {
    try {
      const response = await axiosInstance.get(`/suggestions/${id}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Suggestion not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch suggestion');
      }
      throw error;
    }
  },

  create: async (data: CreateSuggestionRequest): Promise<CreateSuggestionResponse> => {
    try {
      const response = await axiosInstance.post('/suggestions', data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 422) {
          throw new Error('Validation failed');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to create suggestion');
      }
      throw error;
    }
  },

  update: async (id: number, data: UpdateSuggestionRequest): Promise<UpdateSuggestionResponse> => {
    try {
      const response = await axiosInstance.put(`/suggestions/${id}`, data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Suggestion not found');
        }
        if (error.response?.status === 422) {
          throw new Error('Validation failed');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to update suggestion');
      }
      throw error;
    }
  },

  delete: async (id: number): Promise<DeleteSuggestionResponse> => {
    try {
      const response = await axiosInstance.delete(`/suggestions/${id}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Suggestion not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to delete suggestion');
      }
      throw error;
    }
  },

  updateStatus: async (id: number, status: Suggestion['status']): Promise<UpdateSuggestionResponse> => {
    try {
      const response = await axiosInstance.patch(`/suggestions/${id}/status`, { status });
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        if (error.response?.status === 404) {
          throw new Error('Suggestion not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to update suggestion status');
      }
      throw error;
    }
  },

  // Feedback methods
  getAllFeedback: async (suggestionId: number): Promise<GetAllFeedbackResponse> => {
    try {
      const response = await axiosInstance.get(`/suggestions/${suggestionId}/feedback`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('Suggestion not found');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch feedback');
      }
      throw error;
    }
  },

  getFeedbackById: async (suggestionId: number, feedbackId: number): Promise<GetFeedbackResponse> => {
    try {
      const response = await axiosInstance.get(`/suggestions/${suggestionId}/feedback/${feedbackId}`);
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

  createFeedback: async (suggestionId: number, data: CreateFeedbackRequest): Promise<CreateFeedbackResponse> => {
    try {
      const response = await axiosInstance.post(`/suggestions/${suggestionId}/feedback`, data);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        if (error.response?.status === 404) {
          throw new Error('Suggestion not found');
        }
        if (error.response?.status === 422) {
          throw new Error('Validation failed');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to create feedback');
      }
      throw error;
    }
  },

  updateFeedback: async (suggestionId: number, feedbackId: number, data: UpdateFeedbackRequest): Promise<UpdateFeedbackResponse> => {
    try {
      const response = await axiosInstance.put(`/suggestions/${suggestionId}/feedback/${feedbackId}`, data);
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

  deleteFeedback: async (suggestionId: number, feedbackId: number): Promise<DeleteFeedbackResponse> => {
    try {
      const response = await axiosInstance.delete(`/suggestions/${suggestionId}/feedback/${feedbackId}`);
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