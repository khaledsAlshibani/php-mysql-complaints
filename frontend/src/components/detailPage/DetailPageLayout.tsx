import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ChevronLeft } from 'lucide-react';
import Link from 'next/link';
import { format } from 'date-fns';
import { ActionButtons } from './ActionButtons';
import { FeedbackSection } from './FeedbackSection';
import { FeedbackDialogs } from './FeedbackDialogs';
import { ErrorState } from './ErrorState';
import type { ComplaintFeedback } from '@/types/api/complaint';
import type { SuggestionFeedback } from '@/types/api/suggestion';
import type { CommonStatus } from '@/types/api/common';

interface DetailPageLayoutProps<T extends { id: number; content: string; status: string; createdAt: string; user: { id: number; fullName: string }; feedback: F[] }, F extends ComplaintFeedback | SuggestionFeedback> {
  item: T | null;
  isLoading: boolean;
  error: string | null;
  type: 'complaints' | 'suggestions';
  statusColors: Record<string, string>;
  statusIcons: Record<string, React.ComponentType<{ className?: string }>>;
  isAdmin: boolean;
  isOwner: boolean;
  isFeedbackDialogOpen: boolean;
  isUpdateDialogOpen: boolean;
  isDeleteDialogOpen: boolean;
  feedbackContent: string;
  updatedContent: string;
  isSubmitting: boolean;
  isUpdateFeedbackDialogOpen: boolean;
  isDeleteFeedbackDialogOpen: boolean;
  updatedFeedbackContent: string;
  onFeedbackDialogChange: (open: boolean) => void;
  onUpdateDialogChange: (open: boolean) => void;
  onDeleteDialogChange: (open: boolean) => void;
  onFeedbackContentChange: (content: string) => void;
  onUpdatedContentChange: (content: string) => void;
  onAddFeedback: () => void;
  onUpdate: () => void;
  onDelete: () => void;
  onEditFeedback: (feedback: F) => void;
  onDeleteFeedback: (feedback: F) => void;
  onUpdateFeedbackDialogChange: (open: boolean) => void;
  onDeleteFeedbackDialogChange: (open: boolean) => void;
  onUpdatedFeedbackContentChange: (content: string) => void;
  onUpdateFeedback: () => void;
  onConfirmDeleteFeedback: () => void;
  currentStatus?: CommonStatus;
  onStatusChange?: (status: CommonStatus) => void;
}

export function DetailPageLayout<T extends { id: number; content: string; status: string; createdAt: string; user: { id: number; fullName: string }; feedback: F[] }, F extends ComplaintFeedback | SuggestionFeedback>({
  item,
  isLoading,
  error,
  type,
  statusColors,
  statusIcons,
  isAdmin,
  isOwner,
  isFeedbackDialogOpen,
  isUpdateDialogOpen,
  isDeleteDialogOpen,
  feedbackContent,
  updatedContent,
  isSubmitting,
  isUpdateFeedbackDialogOpen,
  isDeleteFeedbackDialogOpen,
  updatedFeedbackContent,
  onFeedbackDialogChange,
  onUpdateDialogChange,
  onDeleteDialogChange,
  onFeedbackContentChange,
  onUpdatedContentChange,
  onAddFeedback,
  onUpdate,
  onDelete,
  onEditFeedback,
  onDeleteFeedback,
  onUpdateFeedbackDialogChange,
  onDeleteFeedbackDialogChange,
  onUpdatedFeedbackContentChange,
  onUpdateFeedback,
  onConfirmDeleteFeedback,
  currentStatus,
  onStatusChange
}: DetailPageLayoutProps<T, F>) {
  if (error) {
    return <ErrorState error={error} type={type} />;
  }

  if (isLoading || !item) {
    return null;
  }

  const StatusIcon = statusIcons[item.status];

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <Button variant="ghost" size="sm" asChild>
          <Link href={`/${type}`} className="flex items-center gap-2 -ml-2">
            <ChevronLeft className="h-4 w-4" />
            Back to {type.charAt(0).toUpperCase() + type.slice(1)}
          </Link>
        </Button>

        <ActionButtons
          isAdmin={isAdmin}
          isOwner={isOwner}
          content={item.content}
          isFeedbackDialogOpen={isFeedbackDialogOpen}
          isUpdateDialogOpen={isUpdateDialogOpen}
          isDeleteDialogOpen={isDeleteDialogOpen}
          feedbackContent={feedbackContent}
          updatedContent={updatedContent}
          isSubmitting={isSubmitting}
          onFeedbackDialogChange={onFeedbackDialogChange}
          onUpdateDialogChange={onUpdateDialogChange}
          onDeleteDialogChange={onDeleteDialogChange}
          onFeedbackContentChange={onFeedbackContentChange}
          onUpdatedContentChange={onUpdatedContentChange}
          onAddFeedback={onAddFeedback}
          onUpdate={onUpdate}
          onDelete={onDelete}
          type={type}
          currentStatus={currentStatus}
          onStatusChange={onStatusChange}
        />
      </div>

      <Card className="overflow-hidden flex flex-col">
        <CardHeader className="space-y-4 border-b">
          <div className="space-y-2">
            <Badge className={`mb-2 w-fit border ${statusColors[item.status]}`}>
              <StatusIcon className="mr-1 h-3 w-3" />
              {item.status
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ')}
            </Badge>
            <CardTitle className="text-xl sm:text-2xl leading-tight">{item.content}</CardTitle>
            <CardDescription className="text-sm">
              Submitted by {item.user.fullName} on{' '}
              {format(new Date(item.createdAt), 'MMMM d, yyyy')}
            </CardDescription>
          </div>
        </CardHeader>
        <CardContent className="flex-1 space-y-8">
          <div className="pt-2">
            <h3 className="text-base font-semibold mb-3">Description</h3>
            <p className="text-sm text-muted-foreground leading-relaxed">{item.content}</p>
          </div>

          <FeedbackSection<F>
            feedback={item.feedback}
            isAdmin={isAdmin}
            onEditFeedback={onEditFeedback}
            onDeleteFeedback={onDeleteFeedback}
          />
        </CardContent>
      </Card>

      <FeedbackDialogs
        isUpdateFeedbackDialogOpen={isUpdateFeedbackDialogOpen}
        isDeleteFeedbackDialogOpen={isDeleteFeedbackDialogOpen}
        updatedFeedbackContent={updatedFeedbackContent}
        isSubmitting={isSubmitting}
        onUpdateFeedbackDialogChange={onUpdateFeedbackDialogChange}
        onDeleteFeedbackDialogChange={onDeleteFeedbackDialogChange}
        onUpdatedFeedbackContentChange={onUpdatedFeedbackContentChange}
        onUpdateFeedback={onUpdateFeedback}
        onDeleteFeedback={onConfirmDeleteFeedback}
      />
    </div>
  );
}