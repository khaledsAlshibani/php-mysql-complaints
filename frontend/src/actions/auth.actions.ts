'use server';

import { SignupSchema } from '@/lib/validations/auth';
import { AuthResponse } from '@/types/auth';
import { AuthService } from '@/services/auth.service';

export async function signUp(formData: FormData): Promise<AuthResponse> {
    const rawFormData = {
        username: formData.get('username'),
        password: formData.get('password'),
        confirm_password: formData.get('confirm_password'),
        first_name: formData.get('first_name'),
        last_name: formData.get('last_name'),
        birth_date: formData.get('birth_date'),
    };

    const validatedFields = SignupSchema.safeParse(rawFormData);

    if (!validatedFields.success) {
        return {
            status: 'error',
            error: {
                message: 'Invalid form data',
                code: 400,
                details: validatedFields.error.errors.map(error => ({
                    field: error.path[0].toString(),
                    issue: error.message
                })),
                errorCode: 'VALIDATION_ERROR'
            }
        };
    }

    const { confirm_password, ...apiData } = validatedFields.data;
    return AuthService.register(apiData);
}
