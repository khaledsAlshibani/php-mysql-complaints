import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';

interface FeedbackDialogsProps {
  isUpdateFeedbackDialogOpen: boolean;
  isDeleteFeedbackDialogOpen: boolean;
  updatedFeedbackContent: string;
  isSubmitting: boolean;
  onUpdateFeedbackDialogChange: (open: boolean) => void;
  onDeleteFeedbackDialogChange: (open: boolean) => void;
  onUpdatedFeedbackContentChange: (content: string) => void;
  onUpdateFeedback: () => void;
  onDeleteFeedback: () => void;
}

export function FeedbackDialogs({
  isUpdateFeedbackDialogOpen,
  isDeleteFeedbackDialogOpen,
  updatedFeedbackContent,
  isSubmitting,
  onUpdateFeedbackDialogChange,
  onDeleteFeedbackDialogChange,
  onUpdatedFeedbackContentChange,
  onUpdateFeedback,
  onDeleteFeedback
}: FeedbackDialogsProps) {
  return (
    <>
      <Dialog open={isUpdateFeedbackDialogOpen} onOpenChange={onUpdateFeedbackDialogChange}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Update Feedback</DialogTitle>
            <DialogDescription>
              Make changes to your feedback here. Click save when you're done.
            </DialogDescription>
          </DialogHeader>
          <div className="py-4">
            <Textarea
              value={updatedFeedbackContent}
              onChange={(e) => onUpdatedFeedbackContentChange(e.target.value)}
              placeholder="Update your feedback..."
              className="min-h-[100px]"
            />
          </div>
          <DialogFooter>
            <Button
              variant="outline"
              onClick={() => onUpdateFeedbackDialogChange(false)}
              disabled={isSubmitting}
            >
              Cancel
            </Button>
            <Button
              onClick={onUpdateFeedback}
              disabled={isSubmitting}
            >
              Save Changes
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <Dialog open={isDeleteFeedbackDialogOpen} onOpenChange={onDeleteFeedbackDialogChange}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Feedback</DialogTitle>
            <DialogDescription>
              Are you sure you want to delete this feedback? This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button
              variant="outline"
              onClick={() => onDeleteFeedbackDialogChange(false)}
              disabled={isSubmitting}
            >
              Cancel
            </Button>
            <Button
              variant="destructive"
              onClick={onDeleteFeedback}
              disabled={isSubmitting}
            >
              Delete Feedback
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  );
}