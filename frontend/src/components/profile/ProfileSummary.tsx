import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { CalendarDays, Mail, Camera } from "lucide-react";
import type { User } from "@/types/api/auth";
import { getDefaultProfilePhoto } from "@/utils/getDefaultProfilePhoto";
import { toast } from "sonner";
import { userService } from "@/services/userService";
import { useState } from "react";

interface ProfileSummaryProps {
    profile: User | null;
    onProfileUpdate?: (updatedProfile: User) => void;
}

export function ProfileSummary({ profile, onProfileUpdate }: ProfileSummaryProps) {
    const [isUploading, setIsUploading] = useState(false);
    const { updatePhoto } = userService();

    const getInitials = (firstName?: string | null, lastName?: string | null) => {
        if (!firstName && !lastName) return "U";
        return `${(firstName?.[0] || "").toUpperCase()}${(lastName?.[0] || "").toUpperCase()}`;
    };

    const handlePhotoChange = async (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (!file) return;

        event.target.value = '';

        try {
            setIsUploading(true);
            const result = await updatePhoto(file);

            if (result.status === 'error') {
                throw new Error(result.error?.message || 'Failed to update photo');
            }

            if (onProfileUpdate && profile) {
                onProfileUpdate({
                    ...profile,
                    photoPath: result.data?.photoPath || null
                });
            }

            toast.success("Profile photo updated successfully");
        } catch (error: any) {
            toast.error(error.message || "Failed to update profile photo");
        } finally {
            setIsUploading(false);
        }
    };

    return (
        <aside className="space-y-6">
            <Card>
                <CardHeader className="space-y-6 flex flex-col items-center">
                    <div className="relative">
                        <Avatar className="w-24 h-24">
                            <AvatarImage 
                                src={getDefaultProfilePhoto(profile?.role, profile?.photoPath)} 
                                className={isUploading ? 'opacity-50' : ''}
                            />
                            <AvatarFallback className="text-xl">
                                {getInitials(profile?.firstName, profile?.lastName)}
                            </AvatarFallback>
                        </Avatar>
                        <label 
                            htmlFor="photo-upload" 
                            className="absolute bottom-0 right-0 p-1 rounded-full bg-secondary hover:bg-secondary/80 cursor-pointer transition-colors"
                        >
                            <Camera className="w-4 h-4" />
                            <input
                                id="photo-upload"
                                type="file"
                                accept="image/jpeg,image/png,image/jpg"
                                className="hidden"
                                onChange={handlePhotoChange}
                                disabled={isUploading}
                            />
                        </label>
                    </div>
                    <div className="space-y-1 text-center">
                        <CardTitle>{profile?.firstName} {profile?.lastName}</CardTitle>
                        <CardDescription className="flex items-center justify-center gap-1">
                            <span className="text-muted-foreground">@</span>
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
    );
}
