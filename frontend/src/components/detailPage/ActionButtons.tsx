import { MessageSquarePlus, Pencil, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { StatusSelect } from './StatusSelect';
import type { CommonStatus } from '@/types/api/common';

interface ActionButtonsProps {
  isAdmin: boolean;
  isOwner: boolean;
  content: string;
  isFeedbackDialogOpen: boolean;
  isUpdateDialogOpen: boolean;
  isDeleteDialogOpen: boolean;
  feedbackContent: string;
  updatedContent: string;
  isSubmitting: boolean;
  onFeedbackDialogChange: (open: boolean) => void;
  onUpdateDialogChange: (open: boolean) => void;
  onDeleteDialogChange: (open: boolean) => void;
  onFeedbackContentChange: (content: string) => void;
  onUpdatedContentChange: (content: string) => void;
  onAddFeedback: () => void;
  onUpdate: () => void;
  onDelete: () => void;
  type: 'complaints' | 'suggestions';
  currentStatus?: CommonStatus;
  onStatusChange?: (status: CommonStatus) => void;
}

export function ActionButtons({
  isAdmin,
  isOwner,
  content,
  isFeedbackDialogOpen,
  isUpdateDialogOpen,
  isDeleteDialogOpen,
  feedbackContent,
  updatedContent,
  isSubmitting,
  onFeedbackDialogChange,
  onUpdateDialogChange,
  onDeleteDialogChange,
  onFeedbackContentChange,
  onUpdatedContentChange,
  onAddFeedback,
  onUpdate,
  onDelete,
  type,
  currentStatus,
  onStatusChange
}: ActionButtonsProps) {
  return (
    <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
      {isAdmin && (
        <>
          {currentStatus && onStatusChange && (
            <StatusSelect
              currentStatus={currentStatus}
              type={type}
              onStatusChange={onStatusChange}
              isAdmin={isAdmin}
            />
          )}
          
          <Dialog open={isFeedbackDialogOpen} onOpenChange={onFeedbackDialogChange}>
            <DialogTrigger asChild>
              <Button
                variant="outline"
                size="sm"
                className="flex items-center justify-center gap-2"
              >
                <MessageSquarePlus className="h-4 w-4" />
                Add Feedback
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Add Feedback</DialogTitle>
                <DialogDescription>
                  Add your feedback to this {type.slice(0, -1)}. The user will be able to see this feedback.
                </DialogDescription>
              </DialogHeader>
              <div className="py-4">
                <Textarea
                  value={feedbackContent}
                  onChange={(e) => onFeedbackContentChange(e.target.value)}
                  placeholder="Write your feedback..."
                  className="min-h-[100px]"
                />
              </div>
              <DialogFooter>
                <Button
                  variant="outline"
                  onClick={() => onFeedbackDialogChange(false)}
                  disabled={isSubmitting}
                >
                  Cancel
                </Button>
                <Button
                  onClick={onAddFeedback}
                  disabled={isSubmitting}
                >
                  Add Feedback
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        </>
      )}

      {isOwner && (
        <div className="flex items-stretch sm:items-center gap-2">
          <Dialog open={isUpdateDialogOpen} onOpenChange={onUpdateDialogChange}>
            <DialogTrigger asChild>
              <Button
                variant="outline"
                size="sm"
                className="flex items-center justify-center gap-2 flex-1 sm:flex-initial"
                onClick={() => onUpdatedContentChange(content)}
              >
                <Pencil className="h-4 w-4" />
                Edit
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Edit {type.slice(0, -1).charAt(0).toUpperCase() + type.slice(0, -1).slice(1)}</DialogTitle>
                <DialogDescription>
                  Make changes to your {type.slice(0, -1)} here. Click save when you&apos;re done.
                </DialogDescription>
              </DialogHeader>
              <div className="py-4">
                <Textarea
                  value={updatedContent}
                  onChange={(e) => onUpdatedContentChange(e.target.value)}
                  placeholder={`Describe your ${type.slice(0, -1)}...`}
                  className="min-h-[100px]"
                />
              </div>
              <DialogFooter>
                <Button
                  variant="outline"
                  onClick={() => onUpdateDialogChange(false)}
                  disabled={isSubmitting}
                >
                  Cancel
                </Button>
                <Button
                  onClick={onUpdate}
                  disabled={isSubmitting}
                >
                  Save Changes
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>

          <Dialog open={isDeleteDialogOpen} onOpenChange={onDeleteDialogChange}>
            <DialogTrigger asChild>
              <Button
                variant="destructive"
                size="sm"
                className="flex items-center justify-center gap-2 flex-1 sm:flex-initial"
              >
                <Trash2 className="h-4 w-4" />
                Delete
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Delete {type.slice(0, -1).charAt(0).toUpperCase() + type.slice(0, -1).slice(1)}</DialogTitle>
                <DialogDescription>
                  Are you sure you want to delete this {type.slice(0, -1)}? This action cannot be undone.
                </DialogDescription>
              </DialogHeader>
              <DialogFooter>
                <Button
                  variant="outline"
                  onClick={() => onDeleteDialogChange(false)}
                  disabled={isSubmitting}
                >
                  Cancel
                </Button>
                <Button
                  variant="destructive"
                  onClick={onDelete}
                  disabled={isSubmitting}
                >
                  Delete {type.slice(0, -1).charAt(0).toUpperCase() + type.slice(0, -1).slice(1)}
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        </div>
      )}
    </div>
  );
}