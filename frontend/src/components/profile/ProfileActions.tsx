"use client";

import { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import type { User } from "@/types/auth";

interface ProfileActionsProps {
    profile: User | null;
}

export function ProfileActions({ profile }: ProfileActionsProps) {
    const [isEditing, setIsEditing] = useState(false);
    const [formData, setFormData] = useState({
        firstName: profile?.firstName || "",
        lastName: profile?.lastName || "",
        birthDate: profile?.birthDate || "",
        photoPath: profile?.photoPath || ""
    });

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        console.log("Update profile with:", formData);
        setIsEditing(false);
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
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="photoPath">Photo URL</Label>
                            <Input
                                id="photoPath"
                                name="photoPath"
                                value={formData.photoPath}
                                onChange={handleInputChange}
                                placeholder="https://example.com/photo.jpg"
                            />
                        </div>
                        <div className="flex gap-2">
                            <Button type="submit">Save Changes</Button>
                            <Button 
                                type="button" 
                                variant="outline" 
                                onClick={() => {
                                    setFormData({
                                        firstName: profile?.firstName || "",
                                        lastName: profile?.lastName || "",
                                        birthDate: profile?.birthDate || "",
                                        photoPath: profile?.photoPath || ""
                                    });
                                    setIsEditing(false);
                                }}
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                ) : (
                    <div className="flex gap-2">
                        <Button onClick={() => setIsEditing(true)}>Edit Profile</Button>
                        <AlertDialog>
                            <AlertDialogTrigger asChild>
                                <Button variant="destructive">Delete Account</Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                    <AlertDialogDescription>
                                        This action cannot be undone. This will permanently delete your
                                        account and remove all your data from our servers.
                                    </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                                    <AlertDialogAction 
                                        className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                        onClick={() => {
                                            console.log("Delete account");
                                        }}
                                    >
                                        Delete Account
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
