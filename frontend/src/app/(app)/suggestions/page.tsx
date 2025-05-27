'use client';

import { useEffect, useState } from 'react';
import { useAuthStore } from '@/store/useAuthStore';
import { suggestionService } from '@/services/suggestionService';
import type { Suggestion } from '@/types/api/suggestion';
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
  pending_with_feedback: 'border-sky-300 text-sky-700 bg-sky-50 dark:border-sky-400 dark:text-sky-300 dark:bg-sky-950/30',
  pending_reviewed: 'border-sky-300 text-sky-700 bg-sky-50 dark:border-sky-400 dark:text-sky-300 dark:bg-sky-950/30',
  resolved: 'border-emerald-300 text-emerald-700 bg-emerald-50 dark:border-emerald-400 dark:text-emerald-300 dark:bg-emerald-950/30',
  ignored: 'border-rose-300 text-rose-700 bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:bg-rose-950/30',
  rejected: 'border-rose-300 text-rose-700 bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:bg-rose-950/30'
} as const;

const statusIcons = {
  pending_no_feedback: Clock,
  pending_with_feedback: AlertCircle,
  pending_reviewed: AlertCircle,
  resolved: CheckCircle2,
  ignored: XCircle,
  rejected: XCircle
} as const;

export default function SuggestionsPage() {
  const { user } = useAuthStore();
  const [suggestions, setSuggestions] = useState<Suggestion[]>([]);
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
    const fetchSuggestions = async () => {
      try {
        setIsLoading(true);
        setError(null);
        const response = await suggestionService.getAll({ 
          search: debouncedSearch || undefined,
          status: statusFilter === 'all' ? undefined : statusFilter
        });
        if (response.status === 'error') {
          throw new Error(response.error.message);
        }
        setSuggestions(response.data);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to fetch suggestions');
      } finally {
        setIsLoading(false);
      }
    };

    fetchSuggestions();
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
        addButtonLink="/suggestions/add"
        addButtonText="Add Suggestion"
        searchPlaceholder="Search suggestions..."
        showAddButton={!!user && user.role !== 'admin'}
      />

      {isLoading ? (
        <LoadingSkeleton />
      ) : suggestions.length === 0 ? (
        <EmptyState
          title="No Suggestions"
          searchQuery={searchQuery}
          isAdmin={!!user && user.role === 'admin'}
          type="suggestions"
        />
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3 auto-rows-fr">
          {suggestions.map((suggestion) => (
            <ItemCard
              key={suggestion.id}
              id={String(suggestion.id)}
              content={suggestion.content}
              status={suggestion.status}
              statusColors={statusColors}
              statusIcons={statusIcons}
              userFullName={suggestion.user.fullName}
              createdAt={suggestion.createdAt}
              isAdmin={!!user && user.role === 'admin'}
              type="suggestions"
            />
          ))}
        </div>
      )}
    </div>
  );
}