import { format } from 'date-fns';
import { MessageSquare, Pencil, Trash2, MoreVertical } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

interface BaseFeedback {
  id: number;
  content: string;
  createdAt: string;
  admin: {
    id: number;
    username: string;
    fullName: string;
  };
}

interface ComplaintFeedback extends BaseFeedback {
  complaintId: number;
  suggestionId?: never;
}

interface SuggestionFeedback extends BaseFeedback {
  suggestionId: number;
  complaintId?: never;
}

type Feedback = ComplaintFeedback | SuggestionFeedback;

interface FeedbackSectionProps<T extends Feedback> {
  feedback: T[];
  isAdmin: boolean;
  onEditFeedback: (feedback: T) => void;
  onDeleteFeedback: (feedback: T) => void;
}

export function FeedbackSection<T extends Feedback>({ 
  feedback, 
  isAdmin, 
  onEditFeedback, 
  onDeleteFeedback 
}: FeedbackSectionProps<T>) {
  if (feedback.length === 0) return null;

  return (
    <div className="space-y-4">
      <h3 className="flex items-center gap-2 text-base font-semibold">
        <MessageSquare className="h-4 w-4" />
        Feedback History ({feedback.length})
      </h3>
      <div className="relative space-y-4 pl-4">
        {feedback.map((item, index) => (
          <div
            key={item.id}
            className={cn(
              "relative",
              "before:absolute before:left-[-12px] before:top-[12px] before:h-[3px] before:w-3 before:bg-border dark:before:bg-border/50",
              "after:absolute after:left-[-16px] after:top-2 after:h-3 after:w-3 after:rounded-full after:border-2 after:border-background after:bg-border after:content-[''] dark:after:border-background dark:after:bg-border/50",
              index === feedback.length - 1 && "pb-0"
            )}
          >
            <div className="rounded-lg border bg-card text-card-foreground shadow-sm transition-colors hover:bg-accent/5">
              <div className="p-4 space-y-3">
                <div className="flex items-start sm:items-center justify-between gap-4">
                  <div className="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-sm">
                    <span className="font-medium text-foreground">
                      {item.admin.fullName}
                    </span>
                    <span className="hidden sm:inline text-muted-foreground">•</span>
                    <time
                      dateTime={item.createdAt}
                      className="text-xs sm:text-sm text-muted-foreground"
                    >
                      {format(new Date(item.createdAt), 'MMM d, yyyy')}
                    </time>
                  </div>
                  {isAdmin && (
                    <DropdownMenu>
                      <DropdownMenuTrigger asChild>
                        <Button
                          variant="ghost"
                          size="sm"
                          className="h-8 w-8 p-0 hover:bg-accent/10 -mr-2"
                        >
                          <MoreVertical className="h-4 w-4" />
                        </Button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="end" className="w-[180px]">
                        <DropdownMenuItem
                          onClick={() => onEditFeedback(item)}
                        >
                          <Pencil className="mr-2 h-4 w-4" />
                          Edit
                        </DropdownMenuItem>
                        <DropdownMenuItem
                          className="text-destructive focus:bg-destructive/10 focus:text-destructive"
                          onClick={() => onDeleteFeedback(item)}
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
                    {item.content}
                  </p>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}