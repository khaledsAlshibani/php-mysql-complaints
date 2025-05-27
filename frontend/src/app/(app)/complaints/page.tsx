'use client';

import { useEffect, useState } from 'react';
import { useAuthStore } from '@/store/useAuthStore';
import { complaintService } from '@/services/complaintService';
import type { Complaint } from '@/types/api/complaint';
import { CommonStatus } from '@/types/api/common';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertCircle, CheckCircle2, Clock, XCircle } from 'lucide-react';
import { useDebounce } from '@/hooks/useDebounce';
import { SearchAndAddSection } from '@/components/listingPage/SearchAndAddSection';
import { LoadingSkeleton } from '@/components/listingPage/LoadingSkeleton';
import { EmptyState } from '@/components/listingPage/EmptyState';
import { ItemCard } from '@/components/listingPage/ItemCard';

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
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<CommonStatus | 'all'>('all');
  const debouncedSearch = useDebounce(searchQuery, 300);

  const statusLabels: Record<CommonStatus, string> = {
    pending_no_feedback: 'Pending (No Feedback)',
    pending_reviewed: 'Pending (Reviewed)',
    resolved: 'Resolved',
    ignored: 'Ignored'
  };

  useEffect(() => {
    const fetchComplaints = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const response = await complaintService.getAll({ 
          search: debouncedSearch || undefined,
          status: statusFilter === 'all' ? undefined : statusFilter
        });
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
  }, [user?.role, debouncedSearch, statusFilter]);

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

  return (
    <div className="space-y-6">
      <SearchAndAddSection
        searchQuery={searchQuery}
        onSearchChange={setSearchQuery}
        statusFilter={statusFilter}
        onStatusChange={setStatusFilter}
        statuses={statusLabels}
        addButtonLink="/complaints/add"
        addButtonText="Add Complaint"
        searchPlaceholder="Search complaints..."
        showAddButton={!!user && user.role !== 'admin'}
      />

      {isLoading ? (
        <LoadingSkeleton />
      ) : complaints.length === 0 ? (
        <EmptyState
          title="No Complaints"
          searchQuery={searchQuery}
          isAdmin={!!user && user.role === 'admin'}
          type="complaints"
        />
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3 auto-rows-fr">
          {complaints.map((complaint) => (
            <ItemCard
              key={complaint.id}
              id={String(complaint.id)}
              content={complaint.content}
              status={complaint.status}
              statusColors={statusColors}
              statusIcons={statusIcons}
              userFullName={complaint.user.fullName}
              createdAt={complaint.createdAt}
              isAdmin={!!user && user.role === 'admin'}
              type="complaints"
            />
          ))}
        </div>
      )}
    </div>
  );
}