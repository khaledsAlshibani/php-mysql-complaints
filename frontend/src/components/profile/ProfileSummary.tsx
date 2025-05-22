import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { CalendarDays, Mail } from "lucide-react";
import type { User } from "@/types/auth";
import { getDefaultProfilePhoto } from "@/utils/getDefaultProfilePhoto";

interface ProfileSummaryProps {
    profile: User | null;
}

export function ProfileSummary({ profile }: ProfileSummaryProps) {
    const getInitials = (firstName?: string | null, lastName?: string | null) => {
        if (!firstName && !lastName) return "U";
        return `${(firstName?.[0] || "").toUpperCase()}${(lastName?.[0] || "").toUpperCase()}`;
    };

    return (
        <aside className="space-y-6">
            <Card>
                <CardHeader className="space-y-6 flex flex-col items-center">
                    <Avatar className="w-24 h-24">
                        <AvatarImage src={getDefaultProfilePhoto(profile?.role, profile?.photoPath)} />
                        <AvatarFallback className="text-xl">
                            {getInitials(profile?.firstName, profile?.lastName)}
                        </AvatarFallback>
                    </Avatar>
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
