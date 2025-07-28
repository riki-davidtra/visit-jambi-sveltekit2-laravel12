// src/lib/auth/authFetch.ts
import { getToken } from '$lib/auth/tokenManager';
import { goto } from '$app/navigation';
import { showToast } from '$lib/utils/toast';

interface FetchOptions extends RequestInit {
    headers?: Record<string, string>;
}

async function fetchAuth(url: string, options: FetchOptions = {}): Promise<Response> {
    const token = getToken();

    if (!token) {
        goto('/login');
        showToast('You are not authenticated. Please log in.', 'error');
        return Promise.reject('Token not available');
    }

    options.headers = {
        ...options.headers,
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    };

    const response = await fetch(url, options);

    if (response.status === 401) {
        showToast('Your session has expired. Please log in again.', 'error');
        goto('/login');
        return Promise.reject('Unauthorized');
    }

    return response;
}

export default fetchAuth;
