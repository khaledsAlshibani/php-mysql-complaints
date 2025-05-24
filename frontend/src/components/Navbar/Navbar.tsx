"use client";

import Link from "next/link"
import {
  NavigationMenu,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  navigationMenuTriggerStyle,
} from "@/components/ui/navigation-menu"
import { Button } from "@/components/ui/button"
import { useRouter } from "next/navigation"
import { Menu, X, User } from "lucide-react"
import { useState, useEffect } from "react"
import { useAuthStore } from "@/store/useAuthStore"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { useLogout } from "@/hooks/useLogout"
import { Skeleton } from "@/components/ui/skeleton"
import ThemeToggle from "./ThemeToggle";
import { cn } from "@/lib/utils"

export function Navbar() {
  const router = useRouter()
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false)
  const { user, isAuthenticated, isLoading } = useAuthStore()
  const { handleLogout, isLoggingOut } = useLogout()
  const userDisplayName = user ? `${user.firstName}${user.lastName && user.lastName !== null ? " " + user.lastName : ""}` : ""

  // Close mobile menu when route changes
  useEffect(() => {
    setIsMobileMenuOpen(false)
  }, [router])

  // Prevent scroll when mobile menu is open
  useEffect(() => {
    if (isMobileMenuOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = 'unset'
    }
    return () => {
      document.body.style.overflow = 'unset'
    }
  }, [isMobileMenuOpen])

  const AuthButtons = () => {
    if (isLoading) {
      return (
        <div className="flex items-center gap-4">
          <Skeleton className="h-10 w-20" />
          <Skeleton className="h-10 w-20" />
        </div>
      );
    }

    if (isAuthenticated) {
      return (
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button variant="ghost" className="gap-2 h-9">
              <User className="h-4 w-4" />
              <span className="line-clamp-1">{userDisplayName}</span>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" className="w-56">
            <DropdownMenuLabel>My Account</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem onClick={() => router.push("/profile")}>
              Profile
            </DropdownMenuItem>
            {user?.role !== 'admin' && (
              <>
                <DropdownMenuItem onClick={() => router.push("/complaints")}>
                  My Complaints
                </DropdownMenuItem>
                <DropdownMenuItem onClick={() => router.push("/suggestions")}>
                  My Suggestions
                </DropdownMenuItem>
              </>
            )}
            <DropdownMenuSeparator />
            <DropdownMenuItem 
              onClick={handleLogout}
              disabled={isLoggingOut}
              className="text-destructive focus:text-destructive"
            >
              {isLoggingOut ? "Logging out..." : "Log out"}
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      );
    }

    return (
      <div className="flex items-center gap-2">
        <Button variant="ghost" onClick={() => router.push("/login")}>
          Login
        </Button>
        <Button onClick={() => router.push("/signup")}>
          Sign Up
        </Button>
      </div>
    );
  };

  return (
    <header className="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <nav className="flex h-14 items-center justify-between">
          <div className="flex items-center gap-2">
            <Link href="/" className="font-semibold">
              Complaints App
            </Link>
          </div>

          {/* Desktop Navigation */}
          <NavigationMenu className="hidden md:flex">
            <NavigationMenuList className="flex items-center gap-1">
              <NavigationMenuItem>
                <Link href="/" legacyBehavior passHref>
                  <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                    Home
                  </NavigationMenuLink>
                </Link>
              </NavigationMenuItem>
              <NavigationMenuItem>
                <Link href="/complaints" legacyBehavior passHref>
                  <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                    Complaints
                  </NavigationMenuLink>
                </Link>
              </NavigationMenuItem>
              <NavigationMenuItem>
                <Link href="/suggestions" legacyBehavior passHref>
                  <NavigationMenuLink className={navigationMenuTriggerStyle()}>
                    Suggestions
                  </NavigationMenuLink>
                </Link>
              </NavigationMenuItem>
            </NavigationMenuList>
          </NavigationMenu>

          {/* Desktop Auth & Theme */}
          <div className="hidden md:flex items-center gap-2">
            <AuthButtons />
            <ThemeToggle />
          </div>

          {/* Mobile Menu Button */}
          <div className="flex items-center gap-2 md:hidden">
            <ThemeToggle />
            <Button
              variant="ghost"
              size="icon"
              className="h-9 w-9"
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            >
              {isMobileMenuOpen ? (
                <X className="h-5 w-5" />
              ) : (
                <Menu className="h-5 w-5" />
              )}
            </Button>
          </div>
        </nav>
      </div>

      {/* Mobile Menu */}
      <div
        className={cn(
          "fixed inset-0 top-14 z-50 md:hidden",
          isMobileMenuOpen ? "animate-in fade-in-0" : "hidden"
        )}
      >
        {/* Backdrop */}
        <div 
          className={cn(
            "fixed inset-0 bg-background/80 backdrop-blur-sm",
            isMobileMenuOpen ? "animate-in fade-in-0" : "hidden"
          )}
          onClick={() => setIsMobileMenuOpen(false)}
        />
        
        {/* Menu Content */}
        <div className="fixed inset-x-0 top-[3.5rem] border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 shadow-lg animate-in slide-in-from-top">
          <div className="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-6">
            <div className="flex flex-col gap-6">
              <div className="flex flex-col gap-3">
                <Link
                  href="/"
                  className="flex items-center text-sm font-medium transition-colors hover:text-primary"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  Home
                </Link>
                <Link
                  href="/complaints"
                  className="flex items-center text-sm font-medium transition-colors hover:text-primary"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  Complaints
                </Link>
                <Link
                  href="/suggestions"
                  className="flex items-center text-sm font-medium transition-colors hover:text-primary"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  Suggestions
                </Link>
              </div>

              <div className="border-t pt-6">
                <AuthButtons />
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  )
}
