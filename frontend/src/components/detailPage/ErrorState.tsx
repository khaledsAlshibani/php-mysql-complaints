import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ChevronLeft } from 'lucide-react';
import Link from 'next/link';

interface ErrorStateProps {
  error: string;
  type: 'complaints' | 'suggestions';
}

export function ErrorState({ error, type }: ErrorStateProps) {
  return (
    <div className="space-y-4">
      <Button variant="ghost" size="sm" asChild>
        <Link href={`/${type}`} className="flex items-center gap-2">
          <ChevronLeft className="h-4 w-4" />
          Back to {type.charAt(0).toUpperCase() + type.slice(1)}
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