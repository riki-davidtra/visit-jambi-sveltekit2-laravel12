<script lang="ts">
	import { goto } from '$app/navigation';
	import { login, isLoggedIn } from '$lib/stores/authStore';
	import { showToast } from '$lib/utils/toast';

	$: {
		if ($isLoggedIn) {
			goto('/admin');
		}
	}

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let name = '';
	let email = '';
	let password = '';
	let password_confirmation = '';
	let message = '';
	let errors = { name: '', email: '', password: '', password_confirmation: '' };
	let loading = false;

	async function registerUser(event: Event) {
		event.preventDefault();
		loading = true;
		message = '';
		errors = { name: '', email: '', password: '', password_confirmation: '' };

		const payload = { name, email, password, password_confirmation };

		try {
			const response = await fetch(`${apiBaseURL}/api/register`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(payload)
			});

			const responseBody = await response.json();

			if (!response.ok) {
				if (responseBody.errors) {
					errors.name = responseBody.errors.name ? responseBody.errors.name[0] : '';
					errors.email = responseBody.errors.email ? responseBody.errors.email[0] : '';
					errors.password = responseBody.errors.password ? responseBody.errors.password[0] : '';
					errors.password_confirmation = responseBody.errors.password_confirmation
						? responseBody.errors.password_confirmation[0]
						: '';
					message = responseBody.message || 'Validation failed.';
				} else {
					message = responseBody.message || 'Registration failed, please try again.';
				}
				showToast(message, 'error');
				return;
			}

			login(responseBody.data.token);
			goto('/admin');
		} catch (error) {
			message = 'An error occurred. Please try again later.';
			showToast(message, 'error');
		} finally {
			loading = false;
		}
	}
</script>

<section>
	<div class="mx-auto max-w-lg pb-2 pt-6">
		<h3 class="mb-4 text-center text-xl font-bold text-gray-800">Register</h3>

		<form on:submit={registerUser}>
			<div class="mb-3 flex flex-col gap-2">
				<label for="name">Name</label>
				<input
					type="text"
					name="name"
					id="name"
					bind:value={name}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.name}
					<p class="text-sm text-red-500">{errors.name}</p>
				{/if}
			</div>
			<div class="mb-3 flex flex-col gap-2">
				<label for="email">Email</label>
				<input
					type="email"
					name="email"
					id="email"
					bind:value={email}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.email}
					<p class="text-sm text-red-500">{errors.email}</p>
				{/if}
			</div>
			<div class="mb-3 flex flex-col gap-2">
				<label for="password">Password</label>
				<input
					type="password"
					name="password"
					id="password"
					bind:value={password}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.password}
					<p class="text-sm text-red-500">{errors.password}</p>
				{/if}
			</div>
			<div class="mb-3 flex flex-col gap-2">
				<label for="password_confirmation">Confirm Password</label>
				<input
					type="password"
					name="password_confirmation"
					id="password_confirmation"
					bind:value={password_confirmation}
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500"
				/>
				{#if errors.password_confirmation}
					<p class="text-sm text-red-500">{errors.password_confirmation}</p>
				{/if}
			</div>
			<div class="mt-6">
				<button
					type="submit"
					disabled={loading}
					class="cursor-pointer rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
				>
					{#if loading}
						Loading...
					{:else}
						Register
					{/if}
				</button>
			</div>
		</form>
	</div>
</section>
