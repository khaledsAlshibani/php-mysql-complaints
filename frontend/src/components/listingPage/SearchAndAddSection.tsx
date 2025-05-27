import { Search, Plus } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import Link from 'next/link';
import { StatusFilter } from './StatusFilter';
import { CommonStatus } from '@/types/api/common';

interface SearchAndAddSectionProps {
  searchQuery: string;
  onSearchChange: (value: string) => void;
  statusFilter: CommonStatus | 'all';
  onStatusChange: (value: CommonStatus | 'all') => void;
  statuses: Record<CommonStatus, string>;
  addButtonLink: string;
  addButtonText: string;
  searchPlaceholder: string;
  showAddButton: boolean;
}

export function SearchAndAddSection({
  searchQuery,
  onSearchChange,
  statusFilter,
  onStatusChange,
  statuses,
  addButtonLink,
  addButtonText,
  searchPlaceholder,
  showAddButton
}: SearchAndAddSectionProps) {
  return (
    <div className="flex items-center gap-4">
      <div className="relative flex-1 max-w-md">
        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        <Input
          placeholder={searchPlaceholder}
          value={searchQuery}
          onChange={(e) => onSearchChange(e.target.value)}
          className="pl-10"
        />
      </div>
      <StatusFilter
        value={statusFilter}
        onValueChange={onStatusChange}
        statuses={statuses}
      />
      {showAddButton && (
        <Link href={addButtonLink}>
          <Button>
            <Plus className="h-4 w-4 mr-2" />
            {addButtonText}
          </Button>
        </Link>
      )}
    </div>
  );
}