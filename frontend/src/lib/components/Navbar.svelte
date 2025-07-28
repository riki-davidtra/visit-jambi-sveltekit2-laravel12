<script lang="ts">
	import { goto } from '$app/navigation';
	import { isLoggedIn, logout } from '$lib/stores/authStore';

	const appName = import.meta.env.VITE_APP_NAME;

	const handleLogout = () => {
		logout();
		goto('/login');
	};
</script>

<nav class="flex items-center bg-purple-800 px-4 py-4 text-white md:px-24">
	<h1 class="text-xl font-bold">{appName}</h1>
	<div class="ml-auto">
		<a href="/" class="mr-4">Home</a>
		<a href="/about" class="mr-4" data-sveltekit-prefetch>About</a>

		{#if $isLoggedIn}
			<a href="/admin" class="mr-4" data-sveltekit-prefetch>Dashboard</a>
			<a href="/admin/me" class="mr-4" data-sveltekit-prefetch>Me</a>
			<a href="/admin/users" class="mr-4" data-sveltekit-prefetch>Users</a>
			<button
				class="cursor-pointer rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
				on:click={handleLogout}
			>
				Logout
			</button>
		{:else}
			<a href="/login" class="mr-4" data-sveltekit-prefetch>Login</a>
			<a href="/register" class="mr-4" data-sveltekit-prefetch>Register</a>
		{/if}
	</div>
</nav>
