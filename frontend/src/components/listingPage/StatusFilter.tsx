import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { CommonStatus } from '@/types/api/common';

interface StatusFilterProps {
  value: CommonStatus | 'all';
  onValueChange: (value: CommonStatus | 'all') => void;
  statuses: Record<CommonStatus, string>;
}

export function StatusFilter({ value, onValueChange, statuses }: StatusFilterProps) {
  return (
    <Select value={value} onValueChange={onValueChange}>
      <SelectTrigger className="w-[180px]">
        <SelectValue placeholder="Filter by status" />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="all">All</SelectItem>
        {Object.entries(statuses).map(([status, label]) => (
          <SelectItem key={status} value={status}>
            {label}
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  );
}