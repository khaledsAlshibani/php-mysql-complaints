import { ApiResponse, PartialResponseUser } from './global';
import { CommonStatus } from './common';

export type ComplaintStatus = CommonStatus;

export interface ComplaintFeedback {
  id: number;
  content: string;
  createdAt: string;
  admin: PartialResponseUser;
  complaintId: number;
}

export interface Complaint {
  id: number;
  content: string;
  status: ComplaintStatus;
  createdAt: string;
  user: PartialResponseUser;
  feedback: ComplaintFeedback[];
}

// Error codes from the backend
export type ComplaintErrorCode = 
  | 'VALIDATION_ERROR'
  | 'COMPLAINT_CREATION_FAILED'
  | 'COMPLAINT_RETRIEVAL_FAILED'
  | 'COMPLAINT_NOT_FOUND'
  | 'UNAUTHORIZED_ACCESS'
  | 'COMPLAINT_UPDATE_FAILED'
  | 'COMPLAINT_DELETE_FAILED';

// Response types for different complaint endpoints
export type GetComplaintResponse = ApiResponse<Complaint, ComplaintErrorCode>;
export type GetComplaintsResponse = ApiResponse<Complaint[], ComplaintErrorCode>;
export type CreateComplaintResponse = ApiResponse<Complaint, ComplaintErrorCode>;
export type UpdateComplaintResponse = ApiResponse<Complaint, ComplaintErrorCode>;
export type DeleteComplaintResponse = ApiResponse<null, ComplaintErrorCode>;

// Request types for complaint operations
export interface CreateComplaintRequest {
  content: string;
}

export interface UpdateComplaintRequest {
  content?: string;
  status?: ComplaintStatus;
}

// Query parameters for getAll
export interface GetAllComplaintsParams {
  search?: string;
  status?: Complaint['status'];
}

// Feedback request types
export interface CreateFeedbackRequest {
  content: string;
}

export interface UpdateFeedbackRequest {
  content: string;
}

// Feedback response types
export type GetFeedbackResponse = ApiResponse<ComplaintFeedback, ComplaintErrorCode>;
export type GetAllFeedbackResponse = ApiResponse<ComplaintFeedback[], ComplaintErrorCode>;
export type CreateFeedbackResponse = ApiResponse<ComplaintFeedback, ComplaintErrorCode>;
export type UpdateFeedbackResponse = ApiResponse<ComplaintFeedback, ComplaintErrorCode>;
export type DeleteFeedbackResponse = ApiResponse<null, ComplaintErrorCode>;