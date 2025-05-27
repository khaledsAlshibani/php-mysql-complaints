'use client';

import { suggestionService } from '@/services/suggestionService';
import { AddItemForm } from '@/components/addPage/AddItemForm';

export default function AddSuggestionPage() {
  const handleSubmit = async (content: string) => {
    const response = await suggestionService.create({ content });
    if (response.status === 'error') {
      throw new Error(response.error.message);
    }
  };

  return (
    <AddItemForm
      type="suggestions"
      onSubmit={handleSubmit}
      title="Submit a Suggestion"
      description="Please provide detailed information about your suggestion for improvement."
      placeholder="Describe your suggestion here..."
      submitButtonText="Submit Suggestion"
    />
  );
} 