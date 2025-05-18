import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

// Define protected routes
const protectedRoutes = ['/profile', '/complaints'];
const authRoutes = ['/login', '/signup'];

export function middleware(request: NextRequest) {
    const { pathname } = request.nextUrl;
    const accessToken = request.cookies.get('access_token');

    // If trying to access auth routes while logged in, redirect to profile
    if (authRoutes.includes(pathname) && accessToken) {
        return NextResponse.redirect(new URL('/profile', request.url));
    }

    // If trying to access protected routes without token, redirect to login
    if (protectedRoutes.includes(pathname) && !accessToken) {
        return NextResponse.redirect(new URL('/login', request.url));
    }

    return NextResponse.next();
}

// Configure which routes to run middleware on
export const config = {
    matcher: [...protectedRoutes, ...authRoutes]
};
