import { ApiResponse, PartialResponseUser } from './global';

export type ComplaintStatus = 'pending_no_feedback' | 'pending_with_feedback' | 'resolved' | 'rejected';

export interface ComplaintFeedback {
  id: number;
  content: string;
  createdAt: string;
  admin: PartialResponseUser;
  complaintId: number | null;
  suggestionId: number | null;
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
  status?: ComplaintStatus;
}