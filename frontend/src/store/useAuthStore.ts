import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import type { UserAuth } from '@/types/api/auth';

interface AuthStore {
    user: UserAuth | null;
    isAuthenticated: boolean;
    isLoading: boolean;
    setUser: (user: UserAuth | null) => void;
    setLoading: (loading: boolean) => void;
    logout: () => void;
}

export const useAuthStore = create<AuthStore>()(
    persist(
        (set) => ({
            user: null,
            isAuthenticated: false,
            isLoading: true,
            setUser: (user) => set({ user, isAuthenticated: !!user }),
            setLoading: (loading) => set({ isLoading: loading }),
            logout: () => set({ user: null, isAuthenticated: false }),
        }),
        {
            name: 'auth-storage',
        }
    )
);