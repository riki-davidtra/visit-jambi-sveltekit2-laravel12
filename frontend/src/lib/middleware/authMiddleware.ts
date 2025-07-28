// src/lib/middleware/authMiddleware.ts
import { getToken, getRemainingTime, setToken, removeToken } from '$lib/auth/tokenManager';
import { isLoggedIn } from '$lib/stores/authStore';
import { goto } from '$app/navigation';
import { showToast } from '$lib/utils/toast';

export async function refreshToken(): Promise<string | null> {
    const token = getToken();
    if (!token) {
        console.log('No Access Token Found');
        return null;
    }

    try {
        const apiBaseURL = import.meta.env.VITE_API_BASE_URL;

        const response = await fetch(`${apiBaseURL}/api/refresh`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });

        if (response.ok) {
            const responseBody = await response.json();
            const newToken = responseBody.data.token;
            setToken(newToken);
            return newToken;
        } else {
            console.log('Failed to refresh token, response not OK.');
            handleTokenExpiration();
        }
    } catch (error) {
        console.error('Failed to refresh token:', error);
        handleTokenExpiration();
    }

    return null;
}

function handleTokenExpiration() {
    removeToken();
    isLoggedIn.set(false);
    goto('/login');
}

export async function authMiddleware(): Promise<boolean> {
    if (typeof window === 'undefined') {
        return true;
    }

    let token = getToken();

    if (!token) {
        showToast('You are not authenticated. Please log in.', 'error');
        goto('/login');
        return false;
    }

    const remainingTime = getRemainingTime(token);
    const minutes = Math.floor(remainingTime / 60);
    const seconds = remainingTime % 60;
    console.log(`Remaining time: ${remainingTime}, Token expires in: ${minutes} menit ${seconds} detik`);

    if (remainingTime <= 600) {
        token = await refreshToken();
        if (!token) {
            showToast('Your session has expired, please log in again.', 'error');
            goto('/login');
            return false;
        }
    } else {
        console.log('Remaining time is more than 10 minute, no need to refresh token.');
    }

    return true;
}

