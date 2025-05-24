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
  GetAllSuggestionsParams
} from '@/types/api/suggestion';

export const suggestionService = {
  getAll: async (params?: GetAllSuggestionsParams): Promise<GetSuggestionsResponse> => {
    try {
      const queryParams = params?.status ? `?status=${params.status}` : '';
      const response = await axiosInstance.get(`/suggestions${queryParams}`);
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

  // Admin-specific endpoints
  getAllAdmin: async (): Promise<GetSuggestionsResponse> => {
    try {
      const response = await axiosInstance.get('/suggestions');
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch suggestions');
      }
      throw error;
    }
  },

  getByStatus: async (status: Suggestion['status']): Promise<GetSuggestionsResponse> => {
    try {
      const response = await axiosInstance.get(`/suggestions/status/${status}`);
      return response.data;
    } catch (error) {
      if (isAxiosError(error)) {
        if (error.response?.status === 403) {
          throw new Error('Access denied. Admin only.');
        }
        throw new Error(error.response?.data?.error?.message || 'Failed to fetch suggestions by status');
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
  }
};