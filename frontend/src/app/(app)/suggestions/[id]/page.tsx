'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter, useSearchParams } from 'next/navigation';
import { useAuthStore } from '@/store/useAuthStore';
import { suggestionService } from '@/services/suggestionService';
import type { Suggestion, SuggestionFeedback, UpdateSuggestionRequest } from '@/types/api/suggestion';
import { toast } from "sonner";
import { DetailPageLayout } from '@/components/detailPage/DetailPageLayout';
import { suggestionStatusColors, suggestionStatusIcons } from '@/constants/status';

export default function SuggestionPage() {
  const params = useParams();
  const router = useRouter();
  const searchParams = useSearchParams();
  const { user } = useAuthStore();
  const [suggestion, setSuggestion] = useState<Suggestion | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [isUpdateDialogOpen, setIsUpdateDialogOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [isFeedbackDialogOpen, setIsFeedbackDialogOpen] = useState(false);
  const [updatedContent, setUpdatedContent] = useState('');
  const [feedbackContent, setFeedbackContent] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [selectedFeedback, setSelectedFeedback] = useState<SuggestionFeedback | null>(null);
  const [isUpdateFeedbackDialogOpen, setIsUpdateFeedbackDialogOpen] = useState(false);
  const [isDeleteFeedbackDialogOpen, setIsDeleteFeedbackDialogOpen] = useState(false);
  const [updatedFeedbackContent, setUpdatedFeedbackContent] = useState('');

  useEffect(() => {
    const fetchSuggestion = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const response = await suggestionService.getById(Number(params.id));
        if (response.status === 'error') {
          throw new Error(response.error.message);
        }
        setSuggestion(response.data);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to fetch suggestion');
      } finally {
        setIsLoading(false);
      }
    };

    fetchSuggestion();
  }, [params.id]);

  useEffect(() => {
    if (searchParams.get('feedback') === 'new' && user?.role === 'admin') {
      setIsFeedbackDialogOpen(true);
    }
  }, [searchParams, user?.role]);

  const handleUpdate = async () => {
    if (!suggestion) return;

    try {
      setIsSubmitting(true);
      const updateData: UpdateSuggestionRequest = {
        content: updatedContent
      };

      const response = await suggestionService.update(suggestion.id, updateData);
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      setSuggestion(response.data);
      setIsUpdateDialogOpen(false);
      toast.success("Suggestion updated successfully");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to update suggestion");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleDelete = async () => {
    if (!suggestion) return;

    try {
      setIsSubmitting(true);
      const response = await suggestionService.delete(suggestion.id);
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      toast.success("Suggestion deleted successfully");
      router.push('/suggestions');
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to delete suggestion");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleAddFeedback = async () => {
    if (!suggestion) return;

    try {
      setIsSubmitting(true);
      const response = await suggestionService.createFeedback(
        suggestion.id,
        { content: feedbackContent }
      );
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      // Refresh suggestion data to get updated feedback
      const suggestionResponse = await suggestionService.getById(suggestion.id);
      if (suggestionResponse.status === 'error') {
        throw new Error(suggestionResponse.error.message);
      }

      setSuggestion(suggestionResponse.data);
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
    if (!selectedFeedback || !suggestion?.id) return;

    try {
      setIsSubmitting(true);
      const response = await suggestionService.updateFeedback(
        suggestion?.id,
        selectedFeedback.id,
        { content: updatedFeedbackContent }
      );
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      const suggestionResponse = await suggestionService.getById(suggestion!.id);
      if (suggestionResponse.status === 'error') {
        throw new Error(suggestionResponse.error.message);
      }

      setSuggestion(suggestionResponse.data);
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
    if (!selectedFeedback || !suggestion?.id) return;

    try {
      setIsSubmitting(true);
      const response = await suggestionService.deleteFeedback(
        suggestion?.id,
        selectedFeedback.id
      );
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      const suggestionResponse = await suggestionService.getById(suggestion!.id);
      if (suggestionResponse.status === 'error') {
        throw new Error(suggestionResponse.error.message);
      }

      setSuggestion(suggestionResponse.data);
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
    if (!suggestion) return;
    try {
      setIsSubmitting(true);
      const response = await suggestionService.updateStatus(suggestion.id, newStatus);
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }
      setSuggestion(response.data);
      toast.success("Status updated successfully");
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Failed to update status");
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <DetailPageLayout<Suggestion, SuggestionFeedback>
      item={suggestion}
      isLoading={isLoading}
      error={error}
      type="suggestions"
      statusColors={suggestionStatusColors}
      statusIcons={suggestionStatusIcons}
      isAdmin={!!user && user.role === 'admin'}
      isOwner={!!user && user.id === suggestion?.user.id}
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
      currentStatus={suggestion?.status}
      onStatusChange={handleStatusChange}
    />
  );
}