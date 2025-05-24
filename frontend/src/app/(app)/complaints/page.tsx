'use client';

import { useEffect, useState } from 'react';
import { useAuthStore } from '@/store/useAuthStore';
import { complaintService } from '@/services/complaintService';
import type { Complaint } from '@/types/api/complaint';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { format } from 'date-fns';
import { AlertCircle, CheckCircle2, Clock, XCircle, MessageSquarePlus } from 'lucide-react';
import Link from 'next/link';

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

export default function ComplaintsPage() {
  const { user } = useAuthStore();
  const [complaints, setComplaints] = useState<Complaint[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchComplaints = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const response = user?.role === 'admin'
          ? await complaintService.getAllAdmin()
          : await complaintService.getAll();
        if (response.status === 'error') {
          throw new Error(response.error.message);
        }
        setComplaints(response.data);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to fetch complaints');
      } finally {
        setIsLoading(false);
      }
    };

    fetchComplaints();
  }, [user?.role]);

  if (error) {
    return (
      <div className="flex items-center justify-center min-h-[50vh]">
        <Card className="w-full max-w-md">
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
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {[...Array(6)].map((_, i) => (
          <Card key={i} className="overflow-hidden">
            <CardHeader>
              <Skeleton className="h-6 w-2/3" />
              <Skeleton className="h-4 w-1/2" />
            </CardHeader>
            <CardContent>
              <Skeleton className="h-20 w-full" />
            </CardContent>
            <CardFooter>
              <Skeleton className="h-4 w-1/3" />
            </CardFooter>
          </Card>
        ))}
      </div>
    );
  }

  if (complaints.length === 0) {
    return (
      <div className="flex items-center justify-center min-h-[50vh]">
        <Card className="w-full max-w-md">
          <CardHeader>
            <CardTitle>No Complaints</CardTitle>
            <CardDescription>
              {user?.role === 'admin'
                ? 'There are no complaints in the system yet.'
                : 'You haven\'t submitted any complaints yet.'}
            </CardDescription>
          </CardHeader>
        </Card>
      </div>
    );
  }

  return (
    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3 auto-rows-fr">
      {complaints.map((complaint) => {
        const StatusIcon = statusIcons[complaint.status];
        return (
          <div key={complaint.id} className="group relative h-full">
            <Link 
              href={`/complaints/${complaint.id}`} 
              className="block h-full"
            >
              <Card className="overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-1 flex flex-col h-full">
                <CardHeader>
                  <div className="flex flex-col gap-2">
                    <div className="flex flex-col gap-6">
                      <Badge className={`w-fit border ${statusColors[complaint.status]}`}>
                        <StatusIcon className="mr-1 h-3 w-3" />
                        {complaint.status
                          .split('_')
                          .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                          .join(' ')}
                      </Badge>
                      <CardTitle className="line-clamp-2">{complaint.content}</CardTitle>
                    </div>
                    <CardDescription>
                      Submitted by {complaint.user.fullName}
                    </CardDescription>
                  </div>
                </CardHeader>
                <CardContent className="flex-1">
                  <p className="line-clamp-3 text-sm text-muted-foreground">
                    {complaint.content}
                  </p>
                </CardContent>
                <CardFooter className="border-t bg-muted/5">
                  <p className="text-xs text-muted-foreground">
                    {format(new Date(complaint.createdAt), 'MMM d, yyyy')}
                  </p>
                </CardFooter>
              </Card>
            </Link>
            {user?.role === 'admin' && (
              <div className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <Link href={`/complaints/${complaint.id}?feedback=new`}>
                  <Button size="icon" variant="secondary" className="h-8 w-8 bg-background/80 backdrop-blur-sm">
                    <MessageSquarePlus className="h-4 w-4" />
                  </Button>
                </Link>
              </div>
            )}
          </div>
        )
      })}
    </div>
  )
}