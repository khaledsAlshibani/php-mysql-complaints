'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter, useSearchParams } from 'next/navigation';
import { useAuthStore } from '@/store/useAuthStore';
import { complaintService } from '@/services/complaintService';
import type { Complaint, ComplaintFeedback, UpdateComplaintRequest } from '@/types/api/complaint';
import { toast } from "sonner";
import { DetailPageLayout } from '@/components/detailPage/DetailPageLayout';
import { complaintStatusColors, complaintStatusIcons } from '@/constants/status';

export default function ComplaintPage() {
  const params = useParams();
  const router = useRouter();
  const searchParams = useSearchParams();
  const { user } = useAuthStore();
  const [complaint, setComplaint] = useState<Complaint | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isUpdateDialogOpen, setIsUpdateDialogOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [isFeedbackDialogOpen, setIsFeedbackDialogOpen] = useState(false);
  const [updatedContent, setUpdatedContent] = useState('');
  const [feedbackContent, setFeedbackContent] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [selectedFeedback, setSelectedFeedback] = useState<ComplaintFeedback | null>(null);
  const [isUpdateFeedbackDialogOpen, setIsUpdateFeedbackDialogOpen] = useState(false);
  const [isDeleteFeedbackDialogOpen, setIsDeleteFeedbackDialogOpen] = useState(false);
  const [updatedFeedbackContent, setUpdatedFeedbackContent] = useState('');

  useEffect(() => {
    const fetchComplaint = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const response = await complaintService.getById(Number(params.id));
        if (response.status === 'error') {
          throw new Error(response.error.message);
        }
        setComplaint(response.data);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to fetch complaint');
      } finally {
        setIsLoading(false);
      }
    };

    fetchComplaint();
  }, [params.id]);

  useEffect(() => {
    if (searchParams.get('feedback') === 'new' && user?.role === 'admin') {
      setIsFeedbackDialogOpen(true);
    }
  }, [searchParams, user?.role]);

  const handleUpdate = async () => {
    if (!complaint) return;

    try {
      setIsSubmitting(true);
      const updateData: UpdateComplaintRequest = {
        content: updatedContent
      };

      const response = await complaintService.update(complaint.id, updateData);
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      setComplaint(response.data);
      setIsUpdateDialogOpen(false);
      toast.success("Complaint updated successfully");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to update complaint");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async () => {
    if (!complaint) return;

    try {
      setIsSubmitting(true);
      const response = await complaintService.delete(complaint.id);
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      toast.success("Complaint deleted successfully");
      router.push('/complaints');
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to delete complaint");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleAddFeedback = async () => {
    if (!complaint) return;

    try {
      setIsSubmitting(true);
      const response = await complaintService.createFeedback(
        complaint.id,
        { content: feedbackContent }
      );
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      const complaintResponse = await complaintService.getById(complaint.id);
      if (complaintResponse.status === 'error') {
        throw new Error(complaintResponse.error.message);
      }

      setComplaint(complaintResponse.data);
      setIsFeedbackDialogOpen(false);
      setFeedbackContent('');
      toast.success("Feedback added successfully");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to add feedback");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleUpdateFeedback = async () => {
    if (!selectedFeedback || !complaint?.id) return;

    try {
      setIsSubmitting(true);
      const response = await complaintService.updateFeedback(
        complaint?.id,
        selectedFeedback.id,
        { content: updatedFeedbackContent }
      );
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      const complaintResponse = await complaintService.getById(complaint!.id);
      if (complaintResponse.status === 'error') {
        throw new Error(complaintResponse.error.message);
      }

      setComplaint(complaintResponse.data);
      setIsUpdateFeedbackDialogOpen(false);
      setSelectedFeedback(null);
      setUpdatedFeedbackContent('');
      toast.success("Feedback updated successfully");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to update feedback");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDeleteFeedback = async () => {
    if (!selectedFeedback || !complaint?.id) return;

    try {
      setIsSubmitting(true);
      const response = await complaintService.deleteFeedback(
        complaint?.id,
        selectedFeedback.id
      );
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      const complaintResponse = await complaintService.getById(complaint!.id);
      if (complaintResponse.status === 'error') {
        throw new Error(complaintResponse.error.message);
      }

      setComplaint(complaintResponse.data);
      setIsDeleteFeedbackDialogOpen(false);
      setSelectedFeedback(null);
      toast.success("Feedback deleted successfully");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to delete feedback");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleStatusChange = async (newStatus: string) => {
    if (!complaint) return;
    try {
      setIsSubmitting(true);
      const response = await complaintService.updateStatus(complaint.id, newStatus);
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }
      setComplaint(response.data);
      toast.success("Status updated successfully");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to update status");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <DetailPageLayout<Complaint, ComplaintFeedback>
      item={complaint}
      isLoading={isLoading}
      error={error}
      type="complaints"
      statusColors={complaintStatusColors}
      statusIcons={complaintStatusIcons}
      isAdmin={!!user && user.role === 'admin'}
      isOwner={!!user && user.id === complaint?.user.id}
      isFeedbackDialogOpen={isFeedbackDialogOpen}
      isUpdateDialogOpen={isUpdateDialogOpen}
      isDeleteDialogOpen={isDeleteDialogOpen}
      feedbackContent={feedbackContent}
      updatedContent={updatedContent}
      isSubmitting={isSubmitting}
      isUpdateFeedbackDialogOpen={isUpdateFeedbackDialogOpen}
      isDeleteFeedbackDialogOpen={isDeleteFeedbackDialogOpen}
      updatedFeedbackContent={updatedFeedbackContent}
      onFeedbackDialogChange={setIsFeedbackDialogOpen}
      onUpdateDialogChange={setIsUpdateDialogOpen}
      onDeleteDialogChange={setIsDeleteDialogOpen}
      onFeedbackContentChange={setFeedbackContent}
      onUpdatedContentChange={setUpdatedContent}
      onAddFeedback={handleAddFeedback}
      onUpdate={handleUpdate}
      onDelete={handleDelete}
      onEditFeedback={(feedback) => {
        setSelectedFeedback(feedback);
        setUpdatedFeedbackContent(feedback.content);
        setIsUpdateFeedbackDialogOpen(true);
      }}
      onDeleteFeedback={(feedback) => {
        setSelectedFeedback(feedback);
        setIsDeleteFeedbackDialogOpen(true);
      }}
      onUpdateFeedbackDialogChange={setIsUpdateFeedbackDialogOpen}
      onDeleteFeedbackDialogChange={setIsDeleteFeedbackDialogOpen}
      onUpdatedFeedbackContentChange={setUpdatedFeedbackContent}
      onUpdateFeedback={handleUpdateFeedback}
      onConfirmDeleteFeedback={handleDeleteFeedback}
      currentStatus={complaint?.status}
      onStatusChange={handleStatusChange}
    />
  );
}