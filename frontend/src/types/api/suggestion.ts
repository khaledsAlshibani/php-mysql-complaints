import { ApiResponse, PartialResponseUser } from './global';

export type SuggestionStatus = 'pending_no_feedback' | 'pending_with_feedback' | 'resolved' | 'rejected';

export interface SuggestionFeedback {
  id: number;
  content: string;
  createdAt: string;
  admin: PartialResponseUser;
  complaintId: number | null;
  suggestionId: number | null;
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
  | 'AUTHENTICATION_REQUIRED'
  | 'INVALID_PAYLOAD'
  | 'VALIDATION_ERROR'
  | 'SUGGESTION_CREATION_FAILED'
  | 'SUGGESTION_RETRIEVAL_FAILED'
  | 'MISSING_ID'
  | 'UNAUTHORIZED_ACCESS'
  | 'SUGGESTION_UPDATE_FAILED'
  | 'SUGGESTION_NOT_FOUND'
  | 'SUGGESTION_DELETE_FAILED'
  | 'ACCESS_DENIED'
  | 'MISSING_STATUS';

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
  status?: SuggestionStatus;
}