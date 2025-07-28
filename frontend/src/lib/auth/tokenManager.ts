// src/lib/auth/tokenManager.ts
import { jwtDecode } from 'jwt-decode';

interface JwtPayload {
    exp: number;
}

export function getToken(): string | null {
    return localStorage.getItem('token');
}

export function setToken(token: string): void {
    localStorage.setItem('token', token);
}

export function removeToken(): void {
    localStorage.removeItem('token');
}

export function decodeToken(token: string): JwtPayload {
    return jwtDecode<JwtPayload>(token);
}

export function getRemainingTime(token: string): number {
    const decoded = decodeToken(token);
    const currentTime = Math.floor(Date.now() / 1000);
    const remainingTime = decoded.exp - currentTime;
    return remainingTime > 0 ? remainingTime : 0;
}