import { AlertCircle, CheckCircle2, Clock, XCircle } from 'lucide-react';
import { CommonStatus } from '@/types/api/common';

export const complaintStatusColors: Record<CommonStatus, string> = {
  [CommonStatus.PendingNoFeedback]: 'border-yellow-300 text-yellow-700 bg-yellow-50 dark:border-yellow-400 dark:text-yellow-300 dark:bg-yellow-950/30',
  [CommonStatus.PendingReviewed]: 'border-sky-300 text-sky-700 bg-sky-50 dark:border-sky-400 dark:text-sky-300 dark:bg-sky-950/30',
  [CommonStatus.Resolved]: 'border-emerald-300 text-emerald-700 bg-emerald-50 dark:border-emerald-400 dark:text-emerald-300 dark:bg-emerald-950/30',
  [CommonStatus.Ignored]: 'border-rose-300 text-rose-700 bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:bg-rose-950/30'
} as const;

export const complaintStatusIcons: Record<CommonStatus, React.ComponentType<{ className?: string }>> = {
  [CommonStatus.PendingNoFeedback]: Clock,
  [CommonStatus.PendingReviewed]: AlertCircle,
  [CommonStatus.Resolved]: CheckCircle2,
  [CommonStatus.Ignored]: XCircle
} as const;

export const suggestionStatusColors: Record<CommonStatus, string> = {
  [CommonStatus.PendingNoFeedback]: 'border-yellow-300 text-yellow-700 bg-yellow-50 dark:border-yellow-400 dark:text-yellow-300 dark:bg-yellow-950/30',
  [CommonStatus.PendingReviewed]: 'border-sky-300 text-sky-700 bg-sky-50 dark:border-sky-400 dark:text-sky-300 dark:bg-sky-950/30',
  [CommonStatus.Resolved]: 'border-emerald-300 text-emerald-700 bg-emerald-50 dark:border-emerald-400 dark:text-emerald-300 dark:bg-emerald-950/30',
  [CommonStatus.Ignored]: 'border-rose-300 text-rose-700 bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:bg-rose-950/30'
} as const;

export const suggestionStatusIcons: Record<CommonStatus, React.ComponentType<{ className?: string }>> = {
  [CommonStatus.PendingNoFeedback]: Clock,
  [CommonStatus.PendingReviewed]: AlertCircle,
  [CommonStatus.Resolved]: CheckCircle2,
  [CommonStatus.Ignored]: XCircle
} as const;