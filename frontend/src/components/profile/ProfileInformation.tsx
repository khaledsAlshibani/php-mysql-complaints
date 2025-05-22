import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import type { User } from "@/types/auth";
import { ProfileActions } from "./ProfileActions";

interface ProfileInformationProps {
    profile: User | null;
}

export function ProfileInformation({ profile }: ProfileInformationProps) {
    return (
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

            <ProfileActions profile={profile} />
        </div>
    );
}
