'use client';

import { useMemo, useEffect, createContext } from "react";
import { authService } from "@/services/authService";
import { isAxiosError } from "axios";
import { useAuthStore } from "@/store/useAuthStore";
import { AuthContextType } from "@/types/api/auth";

export const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const { setUser, setLoading, logout, isLoading } = useAuthStore();

  useEffect(() => {
    const initializeAuth = async () => {
      try {
        const { isAuthenticated } = useAuthStore.getState();
        
        // Only attempt refresh if previously authenticated
        if (!isAuthenticated) {
          setLoading(false);
          return;
        }

        const response = await authService.refresh();
        
        if (response.status === 'success' && response.data) {
          setUser(response.data);
        } else {
          logout();
        }
      } catch (error) {
        if (isAxiosError(error) && error.response?.status !== 401) {
          console.error('Auth initialization error:', error);
        }
        logout();
      } finally {
        setLoading(false);
      }
    };

    initializeAuth();
  }, [setUser, setLoading, logout]);

  const contextValue = useMemo<AuthContextType>(() => {
    return { 
      setUser,
      isLoading 
    };
  }, [setUser, isLoading]);

  return (
    <AuthContext.Provider value={contextValue}>
      {children}
    </AuthContext.Provider>
  );
};