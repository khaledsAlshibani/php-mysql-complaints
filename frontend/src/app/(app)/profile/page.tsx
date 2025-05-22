"use client";

import { useEffect, useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { CalendarDays, Mail, User2 } from "lucide-react";
import { userService } from "@/services/userService";
import { Skeleton } from "@/components/ui/skeleton";
import type { User } from "@/types/auth";

export default function ProfilePage() {
    const [profile, setProfile] = useState<User | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const fetchProfile = async () => {
            try {
                const response = await userService().getProfile();
                if (response.status === "success" && response.data) {
                    setProfile(response.data);
                }
            } catch (error) {
                console.error("Failed to fetch profile:", error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchProfile();
    }, []);

    const getInitials = (firstName?: string | null, lastName?: string | null) => {
        if (!firstName && !lastName) return "U";
        return `${(firstName?.[0] || "").toUpperCase()}${(lastName?.[0] || "").toUpperCase()}`;
    };

    if (isLoading) {
        return <ProfileSkeleton />;
    }

    return (
        <div className="py-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Aside - Profile Summary */}
            <aside className="space-y-6">
                <Card>
                    <CardHeader className="space-y-6 flex flex-col items-center">
                        <Avatar className="w-24 h-24">
                            <AvatarImage src={profile?.photoPath || ""} />
                            <AvatarFallback className="text-xl">
                                {getInitials(profile?.firstName, profile?.lastName)}
                            </AvatarFallback>
                        </Avatar>
                        <div className="space-y-1 text-center">
                            <CardTitle>{profile?.firstName} {profile?.lastName}</CardTitle>
                            <CardDescription className="flex items-center justify-center gap-1">
                                <User2 className="w-4 h-4" />
                                {profile?.username}
                            </CardDescription>
                        </div>
                        <Badge variant="secondary" className="capitalize">
                            {profile?.role}
                        </Badge>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-lg">Account Info</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <CalendarDays className="w-4 h-4" />
                            <span>Joined {profile?.createdAt ? new Date(profile.createdAt).toLocaleDateString() : "N/A"}</span>
                        </div>
                        <div className="flex items-center gap-2 text-sm text-muted-foreground">
                            <Mail className="w-4 h-4" />
                            <span>Username: {profile?.username}</span>
                        </div>
                    </CardContent>
                </Card>
            </aside>

            {/* Main Content */}
            <div className="md:col-span-2 space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Personal Information</CardTitle>
                        <CardDescription>Your personal details and information</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <label className="text-sm font-medium text-muted-foreground">First Name</label>
                                <p className="text-sm">{profile?.firstName}</p>
                            </div>
                            <div className="space-y-1">
                                <label className="text-sm font-medium text-muted-foreground">Last Name</label>
                                <p className="text-sm">{profile?.lastName}</p>
                            </div>
                            <div className="space-y-1">
                                <label className="text-sm font-medium text-muted-foreground">Birth Date</label>
                                <p className="text-sm">{profile?.birthDate ? new Date(profile.birthDate).toLocaleDateString() : "N/A"}</p>
                            </div>
                            <div className="space-y-1">
                                <label className="text-sm font-medium text-muted-foreground">Account Type</label>
                                <p className="text-sm capitalize">{profile?.role}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}

function ProfileSkeleton() {
    return (
        <div className="py-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <aside className="space-y-6">
                <Card>
                    <CardHeader className="space-y-6">
                        <div className="flex justify-center">
                            <Skeleton className="w-24 h-24 rounded-full" />
                        </div>
                        <div className="space-y-2">
                            <Skeleton className="h-4 w-[200px] mx-auto" />
                            <Skeleton className="h-4 w-[150px] mx-auto" />
                        </div>
                        <Skeleton className="h-5 w-[100px] mx-auto" />
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader>
                        <Skeleton className="h-5 w-[150px]" />
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Skeleton className="h-4 w-full" />
                        <Skeleton className="h-4 w-full" />
                    </CardContent>
                </Card>
            </aside>
            <div className="md:col-span-2 space-y-6">
                <Card>
                    <CardHeader>
                        <Skeleton className="h-5 w-[200px]" />
                        <Skeleton className="h-4 w-[300px]" />
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {Array(4).fill(null).map((_, i) => (
                                <div key={i} className="space-y-2">
                                    <Skeleton className="h-4 w-[100px]" />
                                    <Skeleton className="h-4 w-[150px]" />
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
