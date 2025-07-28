// src/lib/stores/authStore.ts
import { writable } from 'svelte/store';

let storedToken: string | null = null;

if (typeof window !== 'undefined') {
    storedToken = localStorage.getItem('token');
}

export const isLoggedIn = writable(!!storedToken);

export function login(token: string) {
    if (typeof window !== 'undefined') {
        localStorage.setItem('token', token);
    }
    isLoggedIn.set(true);
}

export function logout() {
    if (typeof window !== 'undefined') {
        localStorage.removeItem('token');
    }
    isLoggedIn.set(false);
} 
