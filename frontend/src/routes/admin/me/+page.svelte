<script lang="ts">
	import { authMiddleware } from '$lib/middleware/authMiddleware';
	import { onMount } from 'svelte';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import { showToast } from '$lib/utils/toast';
	import authFetch from '$lib/auth/authFetch';

	authMiddleware();

	let breadcrumbItems = [
		{ name: 'Dashboard', url: '/admin' },
		{ name: 'Me', url: '', isActive: true }
	];

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let user: User | null = null;
	let message = '';

	interface User {
		name: string;
		email: string;
	}

	onMount(async () => {
		const token = localStorage.getItem('token');

		if (!token) {
			message = 'You are not authenticated. Please log in.';
			showToast(message, 'error');
			return;
		}

		try {
			const response = await authFetch(`${apiBaseURL}/api/me`, {
				method: 'GET'
			});

			const responseBody = await response.json();

			if (!response.ok) {
				message = 'Failed to fetch user data.';
				showToast(message, 'error');
				return;
			}

			user = responseBody.data.user;
		} catch (error) {
			message = 'Something went wrong, please try again later.';
			showToast(message, 'error');
		}
	});
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		{#if user}
			<p><strong>Name:</strong> {user.name}</p>
			<p><strong>Email:</strong> {user.email}</p>
		{:else}
			<p class="text-center">Loading...</p>
		{/if}
	</div>
</section>
