'use client';

import { useAuthStore } from '@/store/useAuthStore';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function ProfilePage() {
    const { user } = useAuthStore();

    if (!user) {
        return <div>Loading...</div>;
    }

    return (
        <div className="min-h-screen flex items-center justify-center p-4">
            <Card className="w-full max-w-md">
                <CardHeader>
                    <CardTitle className="text-2xl">Profile</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                    <div>
                        <p className="text-sm text-muted-foreground">Username</p>
                        <p className="font-medium">{user.username}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted-foreground">First Name</p>
                        <p className="font-medium">{user.firstName}</p>
                    </div>
                    {user.lastName && (
                        <div>
                            <p className="text-sm text-muted-foreground">Last Name</p>
                            <p className="font-medium">{user.lastName}</p>
                        </div>
                    )}
                    {user.birthDate && (
                        <div>
                            <p className="text-sm text-muted-foreground">Birth Date</p>
                            <p className="font-medium">{user.birthDate}</p>
                        </div>
                    )}
                    <div>
                        <p className="text-sm text-muted-foreground">Role</p>
                        <p className="font-medium capitalize">{user.role}</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
