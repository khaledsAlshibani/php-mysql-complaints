"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/useAuthStore";
import { toast } from "sonner";

export function useAdminGuard() {
    const router = useRouter();
    const { user, isAuthenticated, isLoading } = useAuthStore();

    useEffect(() => {
        // Wait for auth state to be loaded
        if (isLoading) return;

        // If not authenticated, redirect to login
        if (!isAuthenticated) {
            toast.error("You must be logged in to access this page");
            router.push("/login");
            return;
        }

        // If authenticated but not admin, redirect to profile
        if (user?.role !== "admin") {
            toast.error("You don't have permission to access this page");
            router.push("/profile");
            return;
        }
    }, [isAuthenticated, isLoading, router, user?.role]);

    return {
        isAdmin: user?.role === "admin",
        isLoading
    };
}