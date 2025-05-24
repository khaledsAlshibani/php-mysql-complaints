'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter, useSearchParams } from 'next/navigation';
import { useAuthStore } from '@/store/useAuthStore';
import { complaintService } from '@/services/complaintService';
import { feedbackService } from '@/services/feedbackService';
import type { Complaint, UpdateComplaintRequest } from '@/types/api/complaint';
import type { CreateFeedbackRequest, Feedback, UpdateFeedbackRequest } from '@/types/api/feedback';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { format } from 'date-fns';
import { AlertCircle, CheckCircle2, Clock, XCircle, Pencil, Trash2, MessageSquarePlus, MessageSquare, MoreVertical } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { ChevronLeft } from 'lucide-react';
import Link from 'next/link';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { toast } from "sonner";
import { cn } from '@/lib/utils';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

const statusColors = {
  pending_no_feedback: 'border-yellow-300 text-yellow-700 bg-yellow-50 dark:border-yellow-400 dark:text-yellow-300 dark:bg-yellow-950/30',
  pending_reviewed: 'border-sky-300 text-sky-700 bg-sky-50 dark:border-sky-400 dark:text-sky-300 dark:bg-sky-950/30',
  resolved: 'border-emerald-300 text-emerald-700 bg-emerald-50 dark:border-emerald-400 dark:text-emerald-300 dark:bg-emerald-950/30',
  ignored: 'border-rose-300 text-rose-700 bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:bg-rose-950/30'
} as const;

const statusIcons = {
  pending_no_feedback: Clock,
  pending_reviewed: AlertCircle,
  resolved: CheckCircle2,
  ignored: XCircle
} as const;

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
  const [selectedFeedback, setSelectedFeedback] = useState<Feedback | null>(null);
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

  const canModify = user && complaint && (
    user.id === complaint.user.id || 
    user.role === 'admin'
  );

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
      const response = await feedbackService.create({ 
        content: feedbackContent,
        complaint_id: complaint.id 
      });
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      // Refresh complaint data to get updated feedback
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
    if (!selectedFeedback) return;

    try {
      setIsSubmitting(true);
      const response = await feedbackService.update(selectedFeedback.id, { 
        content: updatedFeedbackContent 
      });
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      // Refresh complaint data to get updated feedback
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
    if (!selectedFeedback) return;

    try {
      setIsSubmitting(true);
      const response = await feedbackService.delete(selectedFeedback.id);
      if (response.status === 'error') {
        throw new Error(response.error.message);
      }

      // Refresh complaint data to get updated feedback
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

  if (error) {
    return (
      <div className="space-y-4">
        <Button variant="ghost" size="sm" asChild>
          <Link href="/complaints" className="flex items-center gap-2">
            <ChevronLeft className="h-4 w-4" />
            Back to Complaints
          </Link>
        </Button>
        <Card>
          <CardHeader>
            <CardTitle className="text-red-500">Error</CardTitle>
          </CardHeader>
          <CardContent>
            <p>{error}</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-32" />
        <Card>
          <CardHeader className="space-y-2">
            <Skeleton className="h-4 w-32" />
            <Skeleton className="h-6 w-3/4" />
            <Skeleton className="h-4 w-1/2" />
          </CardHeader>
          <CardContent>
            <Skeleton className="h-24 w-full" />
          </CardContent>
        </Card>
      </div>
    );
  }

  if (!complaint) {
    return null;
  }

  const StatusIcon = statusIcons[complaint.status];

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <Button variant="ghost" size="sm" asChild>
          <Link href="/complaints" className="flex items-center gap-2">
            <ChevronLeft className="h-4 w-4" />
            Back to Complaints
          </Link>
        </Button>

        <div className="flex items-center gap-2">
          {user?.role === 'admin' && (
            <Dialog open={isFeedbackDialogOpen} onOpenChange={setIsFeedbackDialogOpen}>
              <DialogTrigger asChild>
                <Button 
                  variant="outline" 
                  size="sm"
                  className="flex items-center gap-2"
                >
                  <MessageSquarePlus className="h-4 w-4" />
                  Add Feedback
                </Button>
              </DialogTrigger>
              <DialogContent>
                <DialogHeader>
                  <DialogTitle>Add Feedback</DialogTitle>
                  <DialogDescription>
                    Add your feedback to this complaint. The user will be able to see this feedback.
                  </DialogDescription>
                </DialogHeader>
                <div className="py-4">
                  <Textarea
                    value={feedbackContent}
                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setFeedbackContent(e.target.value)}
                    placeholder="Write your feedback..."
                    className="min-h-[100px]"
                  />
                </div>
                <DialogFooter>
                  <Button
                    variant="outline"
                    onClick={() => setIsFeedbackDialogOpen(false)}
                    disabled={isSubmitting}
                  >
                    Cancel
                  </Button>
                  <Button
                    onClick={handleAddFeedback}
                    disabled={isSubmitting}
                  >
                    Add Feedback
                  </Button>
                </DialogFooter>
              </DialogContent>
            </Dialog>
          )}

          {canModify && (
            <div className="flex items-center gap-2">
              <Dialog open={isUpdateDialogOpen} onOpenChange={setIsUpdateDialogOpen}>
                <DialogTrigger asChild>
                  <Button 
                    variant="outline" 
                    size="sm"
                    className="flex items-center gap-2"
                    onClick={() => setUpdatedContent(complaint.content)}
                  >
                    <Pencil className="h-4 w-4" />
                    Edit
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>Edit Complaint</DialogTitle>
                    <DialogDescription>
                      Make changes to your complaint here. Click save when you're done.
                    </DialogDescription>
                  </DialogHeader>
                  <div className="py-4">
                    <Textarea
                      value={updatedContent}
                      onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setUpdatedContent(e.target.value)}
                      placeholder="Describe your complaint..."
                      className="min-h-[100px]"
                    />
                  </div>
                  <DialogFooter>
                    <Button
                      variant="outline"
                      onClick={() => setIsUpdateDialogOpen(false)}
                      disabled={isSubmitting}
                    >
                      Cancel
                    </Button>
                    <Button
                      onClick={handleUpdate}
                      disabled={isSubmitting}
                    >
                      Save Changes
                    </Button>
                  </DialogFooter>
                </DialogContent>
              </Dialog>

              <Dialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
                <DialogTrigger asChild>
                  <Button 
                    variant="destructive" 
                    size="sm"
                    className="flex items-center gap-2"
                  >
                    <Trash2 className="h-4 w-4" />
                    Delete
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>Delete Complaint</DialogTitle>
                    <DialogDescription>
                      Are you sure you want to delete this complaint? This action cannot be undone.
                    </DialogDescription>
                  </DialogHeader>
                  <DialogFooter>
                    <Button
                      variant="outline"
                      onClick={() => setIsDeleteDialogOpen(false)}
                      disabled={isSubmitting}
                    >
                      Cancel
                    </Button>
                    <Button
                      variant="destructive"
                      onClick={handleDelete}
                      disabled={isSubmitting}
                    >
                      Delete Complaint
                    </Button>
                  </DialogFooter>
                </DialogContent>
              </Dialog>
            </div>
          )}
        </div>
      </div>

      <Card>
        <CardHeader className="space-y-4">
          <div>
            <Badge className={`mb-2 w-fit border ${statusColors[complaint.status]}`}>
              <StatusIcon className="mr-1 h-3 w-3" />
              {complaint.status
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ')}
            </Badge>
            <CardTitle className="text-2xl">{complaint.content}</CardTitle>
            <CardDescription>
              Submitted by {complaint.user.fullName} on{' '}
              {format(new Date(complaint.createdAt), 'MMMM d, yyyy')}
            </CardDescription>
          </div>
        </CardHeader>
        <CardContent className="space-y-6">
          <div>
            <h3 className="mb-2 font-semibold">Description</h3>
            <p className="text-muted-foreground">{complaint.content}</p>
          </div>

          {complaint.feedback.length > 0 && (
            <div className="space-y-4">
              <h3 className="flex items-center gap-2 font-semibold">
                <MessageSquare className="h-4 w-4" />
                Feedback History ({complaint.feedback.length})
              </h3>
              <div className="relative space-y-4 pl-4 ">
                {complaint.feedback.map((feedback, index) => (
                  <div 
                    key={feedback.id}
                    className={cn(
                      "relative",
                      "before:absolute before:left-[-12px] before:top-[12px] before:h-[3px] before:w-3 before:bg-border dark:before:bg-border/50",
                      "after:absolute after:left-[-16px] after:top-2 after:h-3 after:w-3 after:rounded-full after:border-2 after:border-background after:bg-border after:content-[''] dark:after:border-background dark:after:bg-border/50",
                      index === complaint.feedback.length - 1 && "pb-0"
                    )}
                  >
                    <div className="rounded-lg border bg-card text-card-foreground shadow-sm transition-colors hover:bg-accent/5">
                      <div className="p-4 space-y-2">
                        <div className="flex items-center justify-between gap-4">
                          <div className="flex flex-col sm:flex-row sm:items-center sm:gap-2 text-sm">
                            <span className="font-medium text-foreground">
                              {feedback.admin.fullName}
                            </span>
                            <span className="hidden sm:inline text-muted-foreground">•</span>
                            <time 
                              dateTime={feedback.createdAt}
                              className="text-muted-foreground"
                            >
                              {format(new Date(feedback.createdAt), 'MMM d, yyyy')}
                            </time>
                          </div>
                          {user?.role === 'admin' && (
                            <DropdownMenu>
                              <DropdownMenuTrigger asChild>
                                <Button 
                                  variant="ghost" 
                                  size="sm" 
                                  className="h-8 w-8 p-0 hover:bg-accent/10"
                                >
                                  <MoreVertical className="h-4 w-4" />
                                </Button>
                              </DropdownMenuTrigger>
                              <DropdownMenuContent align="end">
                                <DropdownMenuItem
                                  onClick={() => {
                                    setSelectedFeedback(feedback);
                                    setUpdatedFeedbackContent(feedback.content);
                                    setIsUpdateFeedbackDialogOpen(true);
                                  }}
                                >
                                  <Pencil className="mr-2 h-4 w-4" />
                                  Edit
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                  className="text-destructive focus:bg-destructive/10 focus:text-destructive"
                                  onClick={() => {
                                    setSelectedFeedback(feedback);
                                    setIsDeleteFeedbackDialogOpen(true);
                                  }}
                                >
                                  <Trash2 className="mr-2 h-4 w-4" />
                                  Delete
                                </DropdownMenuItem>
                              </DropdownMenuContent>
                            </DropdownMenu>
                          )}
                        </div>
                        <div className="prose prose-sm dark:prose-invert max-w-none">
                          <p className="text-sm leading-relaxed">
                            {feedback.content}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Add Update Feedback Dialog */}
      <Dialog open={isUpdateFeedbackDialogOpen} onOpenChange={setIsUpdateFeedbackDialogOpen}>
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
              onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setUpdatedFeedbackContent(e.target.value)}
              placeholder="Update your feedback..."
              className="min-h-[100px]"
            />
          </div>
          <DialogFooter>
            <Button
              variant="outline"
              onClick={() => setIsUpdateFeedbackDialogOpen(false)}
              disabled={isSubmitting}
            >
              Cancel
            </Button>
            <Button
              onClick={handleUpdateFeedback}
              disabled={isSubmitting}
            >
              Save Changes
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Add Delete Feedback Dialog */}
      <Dialog open={isDeleteFeedbackDialogOpen} onOpenChange={setIsDeleteFeedbackDialogOpen}>
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
              onClick={() => setIsDeleteFeedbackDialogOpen(false)}
              disabled={isSubmitting}
            >
              Cancel
            </Button>
            <Button
              variant="destructive"
              onClick={handleDeleteFeedback}
              disabled={isSubmitting}
            >
              Delete Feedback
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}