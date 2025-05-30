'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { toast } from 'sonner';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Button } from '@/components/ui/button';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Eye, EyeOff } from 'lucide-react';
import { authService } from '@/services/authService';
import { SignupSchema } from '@/lib/validations/auth';
import { useAuthStore } from '@/store/useAuthStore';
import { RegisterFormData } from '@/types/api/auth';

export function SignupForm() {
    const router = useRouter();
    const [isPending, setIsPending] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const setUser = useAuthStore((state) => state.setUser);

    const form = useForm<RegisterFormData>({
        resolver: zodResolver(SignupSchema),
        defaultValues: {
            username: '',
            password: '',
            confirmPassword: '',
            firstName: '',
            lastName: '',
            birthDate: ''
        }
    });

    async function onSubmit(data: RegisterFormData) {
        setIsPending(true);
        try {
            const response = await authService.register(data);

            if (response.status === 'success' && response.data) {
                setUser(response.data);
                toast.success('Account created successfully');
                router.push('/profile');
            } else {
                if (response.error?.details) {
                    response.error.details.forEach(({ field, issue }) => {
                        // Convert snake_case field names to user-friendly format
                        const fieldName = field.split('_').map(word => 
                            word.charAt(0).toUpperCase() + word.slice(1)
                        ).join(' ');
                        toast.error(`${fieldName}: ${issue}`);
                    });
                } else {
                    toast.error(response.error?.message || 'Registration failed');
                }
            }
        } catch (error) {
            if (error instanceof Error) {
                toast.error(error.message);
            } else {
                toast.error('An unexpected error occurred');
            }
        } finally {
            setIsPending(false);
        }
    }

    return (
        <div className="min-h-screen flex items-center justify-center p-4">
            <Card className="w-full max-w-md">
                <CardHeader>
                    <CardTitle className="text-2xl">Create an Account</CardTitle>
                    <CardDescription>Enter your details to sign up</CardDescription>
                </CardHeader>
                <CardContent>
                    <Form {...form}>
                        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                            <FormField
                                control={form.control}
                                name="username"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Username</FormLabel>
                                        <FormControl>
                                            <Input placeholder="Enter username" {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <FormField
                                control={form.control}
                                name="password"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Password</FormLabel>
                                        <FormControl>
                                            <div className="relative">
                                                <Input 
                                                    type={showPassword ? "text" : "password"}
                                                    placeholder="Enter password" 
                                                    {...field} 
                                                />
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                                                    onClick={() => setShowPassword(!showPassword)}
                                                >
                                                    {showPassword ? (
                                                        <EyeOff className="h-4 w-4" />
                                                    ) : (
                                                        <Eye className="h-4 w-4" />
                                                    )}
                                                </Button>
                                            </div>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <FormField
                                control={form.control}
                                name="confirmPassword"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Confirm Password</FormLabel>
                                        <FormControl>
                                            <div className="relative">
                                                <Input 
                                                    type={showConfirmPassword ? "text" : "password"}
                                                    placeholder="Confirm your password" 
                                                    {...field} 
                                                />
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                                                    onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                                >
                                                    {showConfirmPassword ? (
                                                        <EyeOff className="h-4 w-4" />
                                                    ) : (
                                                        <Eye className="h-4 w-4" />
                                                    )}
                                                </Button>
                                            </div>
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <FormField
                                    control={form.control}
                                    name="firstName"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>First Name</FormLabel>
                                            <FormControl>
                                                <Input placeholder="Enter first name" {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name="lastName"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Last Name</FormLabel>
                                            <FormControl>
                                                <Input placeholder="Enter last name" {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                            </div>

                            <FormField
                                control={form.control}
                                name="birthDate"
                                render={({ field }) => (
                                    <FormItem>
                                        <FormLabel>Birth Date</FormLabel>
                                        <FormControl>
                                            <Input type="date" {...field} />
                                        </FormControl>
                                        <FormMessage />
                                    </FormItem>
                                )}
                            />

                            <Button type="submit" className="w-full" disabled={isPending}>
                                {isPending ? "Creating account..." : "Sign Up"}
                            </Button>

                            <div className="text-center text-sm text-muted-foreground mt-4">
                                Already have an account?{" "}
                                <Link href="/login" className="text-primary hover:underline">
                                    Login here
                                </Link>
                            </div>
                        </form>
                    </Form>
                </CardContent>
            </Card>
        </div>
    );
} 