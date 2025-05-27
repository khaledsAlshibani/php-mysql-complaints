'use client';

import { complaintService } from '@/services/complaintService';
import { AddItemForm } from '@/components/addPage/AddItemForm';

export default function AddComplaintPage() {
  const handleSubmit = async (content: string) => {
    const response = await complaintService.create({ content });
    if (response.status === 'error') {
      throw new Error(response.error.message);
    }
  };

  return (
    <AddItemForm
      type="complaints"
      onSubmit={handleSubmit}
      title="Submit a Complaint"
      description="Please provide detailed information about your complaint."
      placeholder="Describe your complaint here..."
      submitButtonText="Submit Complaint"
    />
  );
}