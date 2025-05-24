import { ApiResponse, PartialResponseUser } from './global';

export interface Feedback {
  id: number;
  content: string;
  createdAt: string;
  admin: PartialResponseUser;
  complaintId: number | null;
  suggestionId: number | null;
}

// Error codes from the backend
export type FeedbackErrorCode = 
  | 'AUTHENTICATION_REQUIRED'
  | 'ACCESS_DENIED'
  | 'INVALID_PAYLOAD'
  | 'VALIDATION_ERROR'
  | 'FEEDBACK_CREATION_FAILED'
  | 'FEEDBACK_RETRIEVAL_FAILED'
  | 'MISSING_ID'
  | 'FEEDBACK_NOT_FOUND'
  | 'UNAUTHORIZED_ACCESS'
  | 'FEEDBACK_UPDATE_FAILED'
  | 'FEEDBACK_DELETE_FAILED'
  | 'MISSING_COMPLAINT_ID'
  | 'COMPLAINT_NOT_FOUND'
  | 'MISSING_SUGGESTION_ID'
  | 'SUGGESTION_NOT_FOUND';

// Response types for different feedback endpoints
export type GetFeedbackResponse = ApiResponse<Feedback, FeedbackErrorCode>;
export type GetFeedbacksResponse = ApiResponse<Feedback[], FeedbackErrorCode>;
export type CreateFeedbackResponse = ApiResponse<Feedback, FeedbackErrorCode>;
export type UpdateFeedbackResponse = ApiResponse<Feedback, FeedbackErrorCode>;
export type DeleteFeedbackResponse = ApiResponse<Feedback, FeedbackErrorCode>; // Returns feedback data on deletion

// Request types for feedback operations
export interface CreateFeedbackRequest {
  content: string;
  complaint_id?: number;
  suggestion_id?: number;
}

export interface UpdateFeedbackRequest {
  content: string;
}

// Query parameters for different get operations
export interface GetFeedbackParams {
  id: number;
}

export interface GetComplaintFeedbackParams {
  id: number; // complaint id
}

export interface GetSuggestionFeedbackParams {
  id: number; // suggestion id
}

// Utility type for validation errors
export interface ValidationError {
  field: string;
  issue: string;
}