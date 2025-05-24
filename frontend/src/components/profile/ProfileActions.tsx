"use client";

import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import type { User } from "@/types/auth";
import { userService } from "@/services/userService";

interface ProfileActionsProps {
    profile: User | null;
    onProfileUpdate: (updatedProfile: User) => void;
}

export function ProfileActions({ profile, onProfileUpdate }: ProfileActionsProps) {
    const router = useRouter();
    const [isEditing, setIsEditing] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [formData, setFormData] = useState({
        firstName: profile?.firstName || "",
        lastName: profile?.lastName || "",
        birthDate: profile?.birthDate || "",
    });
    const [deletePassword, setDeletePassword] = useState("");

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const response = await userService().updateProfile(formData);
            if (response.status === "success" && response.data) {
                onProfileUpdate(response.data);
                toast.success("Profile updated successfully");
                setIsEditing(false);
            } else {
                toast.error(response.error?.message || "Failed to update profile");
            }
        } catch (error) {
            toast.error("An error occurred while updating profile");
        } finally {
            setIsLoading(false);
        }
    };

    const handleDelete = async () => {
        setIsLoading(true);

        try {
            const response = await userService().deleteAccount({ password: deletePassword });
            if (response.status === "success") {
                toast.success("Account deleted successfully");
                // Redirect to login page after successful deletion
                router.push("/login");
            } else {
                toast.error(response.error?.message || "Failed to delete account");
            }
        } catch (error) {
            toast.error("An error occurred while deleting account");
        } finally {
            setIsLoading(false);
            setDeletePassword("");
        }
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Profile Actions</CardTitle>
                <CardDescription>Update your profile information or delete your account</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                {isEditing ? (
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="firstName">First Name</Label>
                            <Input
                                id="firstName"
                                name="firstName"
                                value={formData.firstName}
                                onChange={handleInputChange}
                                placeholder="Enter your first name"
                                disabled={isLoading}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="lastName">Last Name</Label>
                            <Input
                                id="lastName"
                                name="lastName"
                                value={formData.lastName}
                                onChange={handleInputChange}
                                placeholder="Enter your last name"
                                disabled={isLoading}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="birthDate">Birth Date</Label>
                            <Input
                                id="birthDate"
                                name="birthDate"
                                type="date"
                                value={formData.birthDate}
                                onChange={handleInputChange}
                                disabled={isLoading}
                            />
                        </div>
                        <div className="flex gap-2">
                            <Button type="submit" disabled={isLoading}>
                                {isLoading ? "Saving..." : "Save Changes"}
                            </Button>
                            <Button 
                                type="button" 
                                variant="outline" 
                                onClick={() => {
                                    setFormData({
                                        firstName: profile?.firstName || "",
                                        lastName: profile?.lastName || "",
                                        birthDate: profile?.birthDate || "",
                                    });
                                    setIsEditing(false);
                                }}
                                disabled={isLoading}
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                ) : (
                    <div className="flex gap-2">
                        <Button onClick={() => setIsEditing(true)} disabled={isLoading}>Edit Profile</Button>
                        <AlertDialog>
                            <AlertDialogTrigger asChild>
                                <Button variant="destructive" disabled={isLoading}>Delete Account</Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        This action cannot be undone. This will permanently delete your
                                        account and remove all your data from our servers.
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <div className="my-4">
                                    <Label htmlFor="deletePassword">Enter your password to confirm</Label>
                                    <Input
                                        id="deletePassword"
                                        type="password"
                                        value={deletePassword}
                                        onChange={(e) => setDeletePassword(e.target.value)}
                                        placeholder="Enter your password"
                                        className="mt-2"
                                    />
                                </div>
                                <AlertDialogFooter>
                                    <AlertDialogCancel onClick={() => setDeletePassword("")}>Cancel</AlertDialogCancel>
                                    <AlertDialogAction 
                                        className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                        onClick={handleDelete}
                                        disabled={!deletePassword || isLoading}
                                    >
                                        {isLoading ? "Deleting..." : "Delete Account"}
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
