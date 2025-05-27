import { ApiResponse, PartialResponseUser } from './global';
import { CommonStatus } from './common';

export type SuggestionStatus = CommonStatus;

export interface SuggestionFeedback {
  id: number;
  content: string;
  createdAt: string;
  admin: PartialResponseUser;
  suggestionId: number;
}

export interface Suggestion {
  id: number;
  content: string;
  status: SuggestionStatus;
  createdAt: string;
  user: PartialResponseUser;
  feedback: SuggestionFeedback[];
}

// Error codes from the backend
export type SuggestionErrorCode = 
  | 'VALIDATION_ERROR'
  | 'COMPLAINT_CREATION_FAILED'
  | 'COMPLAINT_RETRIEVAL_FAILED'
  | 'COMPLAINT_NOT_FOUND'
  | 'UNAUTHORIZED_ACCESS'
  | 'COMPLAINT_UPDATE_FAILED'
  | 'COMPLAINT_DELETE_FAILED';

// Response types for different suggestion endpoints
export type GetSuggestionResponse = ApiResponse<Suggestion, SuggestionErrorCode>;
export type GetSuggestionsResponse = ApiResponse<Suggestion[], SuggestionErrorCode>;
export type CreateSuggestionResponse = ApiResponse<Suggestion, SuggestionErrorCode>;
export type UpdateSuggestionResponse = ApiResponse<Suggestion, SuggestionErrorCode>;
export type DeleteSuggestionResponse = ApiResponse<null, SuggestionErrorCode>;

// Request types for suggestion operations
export interface CreateSuggestionRequest {
  content: string;
}

export interface UpdateSuggestionRequest {
  content?: string;
  status?: SuggestionStatus;
}

// Query parameters for getAll
export interface GetAllSuggestionsParams {
  search?: string;
  status?: Suggestion['status'];
}

// Feedback request types
export interface CreateFeedbackRequest {
  content: string;
}

export interface UpdateFeedbackRequest {
  content: string;
}

// Feedback response types
export type GetFeedbackResponse = ApiResponse<SuggestionFeedback, SuggestionErrorCode>;
export type GetAllFeedbackResponse = ApiResponse<SuggestionFeedback[], SuggestionErrorCode>;
export type CreateFeedbackResponse = ApiResponse<SuggestionFeedback, SuggestionErrorCode>;
export type UpdateFeedbackResponse = ApiResponse<SuggestionFeedback, SuggestionErrorCode>;
export type DeleteFeedbackResponse = ApiResponse<null, SuggestionErrorCode>;