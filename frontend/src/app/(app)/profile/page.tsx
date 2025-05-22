"use client";

import { useEffect, useState } from "react";
import { userService } from "@/services/userService";
import type { User } from "@/types/auth";
import { ProfileSkeleton } from "@/components/profile/ProfileSkeleton";
import { ProfileSummary } from "@/components/profile/ProfileSummary";
import { ProfileInformation } from "@/components/profile/ProfileInformation";

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

    if (isLoading) {
        return <ProfileSkeleton />;
    }

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <ProfileSummary profile={profile} />
            <ProfileInformation profile={profile} />
        </div>
    );
}
