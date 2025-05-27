import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { MessageSquarePlus } from 'lucide-react';
import Link from 'next/link';
import { format } from 'date-fns';

interface ItemCardProps {
  id: string;
  content: string;
  status: string;
  statusColors: Record<string, string>;
  statusIcons: Record<string, React.ComponentType<{ className?: string }>>;
  userFullName: string;
  createdAt: string;
  isAdmin: boolean;
  type: 'complaints' | 'suggestions';
}

export function ItemCard({
  id,
  content,
  status,
  statusColors,
  statusIcons,
  userFullName,
  createdAt,
  isAdmin,
  type
}: ItemCardProps) {
  const StatusIcon = statusIcons[status];
  
  return (
    <div className="group relative h-full">
      <Link 
        href={`/${type}/${id}`} 
        className="block h-full"
      >
        <Card className="overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-1 flex flex-col h-full">
          <CardHeader>
            <div className="flex flex-col gap-2">
              <div className="flex flex-col gap-6">
                <Badge className={`w-fit border ${statusColors[status]}`}>
                  <StatusIcon className="mr-1 h-3 w-3" />
                  {status
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ')}
                </Badge>
                <CardTitle className="line-clamp-2">{content}</CardTitle>
              </div>
              <CardDescription>
                Submitted by {userFullName}
              </CardDescription>
            </div>
          </CardHeader>
          <CardContent className="flex-1">
            <p className="line-clamp-3 text-sm text-muted-foreground">
              {content}
            </p>
          </CardContent>
          <CardFooter className="border-t bg-muted/5">
            <p className="text-xs text-muted-foreground">
              {format(new Date(createdAt), 'MMM d, yyyy')}
            </p>
          </CardFooter>
        </Card>
      </Link>
      {isAdmin && (
        <div className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
          <Link href={`/${type}/${id}?feedback=new`}>
            <Button size="icon" variant="secondary" className="h-8 w-8 bg-background/80 backdrop-blur-sm">
              <MessageSquarePlus className="h-4 w-4" />
            </Button>
          </Link>
        </div>
      )}
    </div>
  );
}