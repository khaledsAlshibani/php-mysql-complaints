import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface EmptyStateProps {
  title: string;
  searchQuery: string;
  isAdmin: boolean;
  type: 'complaints' | 'suggestions';
}

export function EmptyState({ title, searchQuery, isAdmin, type }: EmptyStateProps) {
  return (
    <div className="flex items-center justify-center min-h-[50vh]">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardTitle>{title}</CardTitle>
          <CardDescription>
            {searchQuery 
              ? `No ${type} found matching your search.`
              : isAdmin
                ? `There are no ${type} in the system yet.`
                : `You haven't submitted any ${type} yet.`}
          </CardDescription>
        </CardHeader>
      </Card>
    </div>
  );
}
