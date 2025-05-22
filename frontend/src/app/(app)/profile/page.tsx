"use client";

import { useEffect, useState } from "react";
import { userService } from "@/services/userService";
import type { User } from "@/types/auth";
import { ProfileSkeleton } from "@/components/profile/ProfileSkeleton";
import { ProfileSummary } from "@/components/profile/ProfileSummary";
import { ProfileInformation } from "@/components/profile/ProfileInformation";
import { toast } from "sonner";

export default function ProfilePage() {
    const [profile, setProfile] = useState<User | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    const fetchProfile = async () => {
        try {
            const response = await userService().getProfile();
            if (response.status === "success" && response.data) {
                setProfile(response.data);
            } else {
                toast.error(response.error?.message || "Failed to fetch profile");
            }
        } catch (error) {
            console.error("Failed to fetch profile:", error);
            toast.error("An error occurred while fetching profile");
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchProfile();
    }, []);

    const handleProfileUpdate = (updatedProfile: User) => {
        setProfile(updatedProfile);
    };

    if (isLoading) {
        return <ProfileSkeleton />;
    }

    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <ProfileSummary profile={profile} />
            <ProfileInformation profile={profile} onProfileUpdate={handleProfileUpdate} />
        </div>
    );
}
