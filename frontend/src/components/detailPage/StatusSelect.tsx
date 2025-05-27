import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { complaintStatusColors, complaintStatusIcons } from "@/constants/status";
import { suggestionStatusColors, suggestionStatusIcons } from "@/constants/status";
import { cn } from "@/lib/utils";
import { CommonStatus } from "@/types/api/common";
import { Circle } from "lucide-react";

interface StatusSelectProps {
  currentStatus: CommonStatus;
  type: 'complaints' | 'suggestions';
  onStatusChange: (status: CommonStatus) => void;
  isAdmin: boolean;
}

export function StatusSelect({ currentStatus, type, onStatusChange, isAdmin }: StatusSelectProps) {
  if (!isAdmin) return null;

  const statusIcons = type === 'complaints' ? complaintStatusIcons : suggestionStatusIcons;

  const statuses = [
    CommonStatus.PendingNoFeedback,
    CommonStatus.PendingReviewed,
    CommonStatus.Resolved,
    CommonStatus.Ignored
  ];

  const renderIcon = (status: CommonStatus) => {
    const Icon = statusIcons[status];
    if (!Icon) return <Circle className="h-4 w-4" />;
    return <Icon className="h-4 w-4" />;
  };

  const formatStatus = (status: CommonStatus) => {
    return status
      .split('_')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };

  return (
    <Select
      value={currentStatus}
      onValueChange={(value) => onStatusChange(value as CommonStatus)}
    >
      <SelectTrigger className="w-[200px] truncate">
        <SelectValue placeholder="Change status" />
      </SelectTrigger>
      <SelectContent>
        {statuses.map((status) => (
          <SelectItem key={status} value={status}>
            <div className="flex items-center gap-2">
              {renderIcon(status)}
              <span className="truncate max-w-[140px]">{formatStatus(status)}</span>
            </div>
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  );
}