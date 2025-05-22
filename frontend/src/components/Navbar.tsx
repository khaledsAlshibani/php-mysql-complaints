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
import { Menu, User } from "lucide-react"
import { useState } from "react"
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

export function Navbar() {
  const router = useRouter()
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false)
  const { user, isAuthenticated } = useAuthStore()
  const { handleLogout, isLoggingOut } = useLogout()

  const userDisplayName = user ? `${user.firstName} ${user.lastName}` : ""

  return (
    <div className="border-b">
      <div className="container max-w-screen-lg mx-auto">
        <div className="flex h-16 items-center justify-between">
          {/* Desktop Navigation */}
          <NavigationMenu className="hidden md:flex">
            <NavigationMenuList className="flex items-center gap-2">
              <NavigationMenuItem>
                <NavigationMenuLink asChild className={navigationMenuTriggerStyle()}>
                  <Link href="/">Home</Link>
                </NavigationMenuLink>
              </NavigationMenuItem>
              <NavigationMenuItem>
                <NavigationMenuLink asChild className={navigationMenuTriggerStyle()}>
                  <Link href="/complaints">Complaints</Link>
                </NavigationMenuLink>
              </NavigationMenuItem>
              <NavigationMenuItem>
                <NavigationMenuLink asChild className={navigationMenuTriggerStyle()}>
                  <Link href="/profile">Profile</Link>
                </NavigationMenuLink>
              </NavigationMenuItem>
            </NavigationMenuList>
          </NavigationMenu>

          {/* Mobile Menu Button */}
          <Button
            variant="ghost"
            size="icon"
            className="md:hidden"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            <Menu className="h-5 w-5" />
          </Button>

          {/* Desktop Auth Buttons or User Menu */}
          <div className="hidden md:flex items-center gap-4">
            {isAuthenticated ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" className="gap-2">
                    <User className="h-4 w-4" />
                    <span>Hi, {userDisplayName}</span>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuLabel>My Account</DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem onClick={() => router.push("/profile")}>
                    Profile
                  </DropdownMenuItem>
                  <DropdownMenuItem onClick={() => router.push("/complaints")}>
                    My Complaints
                  </DropdownMenuItem>
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
            ) : (
              <>
                <Button variant="outline" onClick={() => router.push("/login")}>
                  Login
                </Button>
                <Button onClick={() => router.push("/signup")}>
                  Sign Up
                </Button>
              </>
            )}
          </div>

          {/* Mobile Menu */}
          {isMobileMenuOpen && (
            <div className="absolute top-16 left-0 right-0 bg-background border-b md:hidden">
              <div className="container mx-auto px-4 py-4 space-y-4">
                <Link href="/" className="block py-2 hover:text-primary">
                  Home
                </Link>
                <Link href="/complaints" className="block py-2 hover:text-primary">
                  Complaints
                </Link>
                <Link href="/profile" className="block py-2 hover:text-primary">
                  Profile
                </Link>
                <div className="flex flex-col gap-2 pt-4 border-t">
                  {isAuthenticated ? (
                    <>
                      <div className="py-2 text-sm text-muted-foreground">
                        Hi, {userDisplayName}
                      </div>
                      <Button variant="outline" className="w-full" onClick={() => router.push("/profile")}>
                        Profile
                      </Button>
                      <Button 
                        className="w-full text-destructive hover:text-destructive" 
                        variant="ghost"
                        onClick={handleLogout}
                        disabled={isLoggingOut}
                      >
                        {isLoggingOut ? "Logging out..." : "Log out"}
                      </Button>
                    </>
                  ) : (
                    <>
                      <Button variant="outline" className="w-full" onClick={() => router.push("/login")}>
                        Login
                      </Button>
                      <Button className="w-full" onClick={() => router.push("/signup")}>
                        Sign Up
                      </Button>
                    </>
                  )}
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
