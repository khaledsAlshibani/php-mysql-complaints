import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { useAuthStore } from '@/store/useAuthStore';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { toast } from 'sonner';
import { Loader2 } from 'lucide-react';

interface AddItemFormProps {
  type: 'complaints' | 'suggestions';
  onSubmit: (content: string) => Promise<void>;
  title: string;
  description: string;
  placeholder: string;
  submitButtonText: string;
}

export function AddItemForm({
  type,
  onSubmit,
  title,
  description,
  placeholder,
  submitButtonText
}: AddItemFormProps) {
  const router = useRouter();
  const { user, isAuthenticated, isLoading } = useAuthStore();
  const [content, setContent] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (!isLoading) {
      if (!isAuthenticated) {
        router.replace('/login');
      } else if (user?.role === 'admin') {
        router.replace(`/${type}`);
      }
    }
  }, [isAuthenticated, isLoading, router, user?.role, type]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!content.trim()) {
      toast.error(`Please enter your ${type.slice(0, -1)} content.`);
      return;
    }

    try {
      setIsSubmitting(true);
      await onSubmit(content);
      toast.success(`Your ${type.slice(0, -1)} has been submitted successfully.`);
      router.push(`/${type}`);
    } catch (error) {
      toast.error(error instanceof Error ? error.message : `Failed to submit ${type.slice(0, -1)}`);
    } finally {
      setIsSubmitting(false);
    }
  };

  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-[50vh]">
        <Loader2 className="h-8 w-8 animate-spin" />
      </div>
    );
  }

  if (!isAuthenticated || user?.role === 'admin') {
    return null;
  }

  return (
    <form onSubmit={handleSubmit}>
      <Card>
        <CardHeader>
          <CardTitle>{title}</CardTitle>
          <CardDescription>
            {description}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <Textarea
              placeholder={placeholder}
              value={content}
              onChange={(e) => setContent(e.target.value)}
              className="min-h-[200px]"
              disabled={isSubmitting}
            />
          </div>
        </CardContent>
        <CardFooter className="flex justify-between">
          <Button
            type="button"
            variant="outline"
            onClick={() => router.back()}
            disabled={isSubmitting}
          >
            Cancel
          </Button>
          <Button type="submit" disabled={isSubmitting}>
            {isSubmitting ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                Submitting...
              </>
            ) : (
              submitButtonText
            )}
          </Button>
        </CardFooter>
      </Card>
    </form>
  );
}