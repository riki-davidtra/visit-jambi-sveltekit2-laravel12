<script lang="ts">
	import { authMiddleware } from '$lib/middleware/authMiddleware';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import Button from '$lib/components/Button.svelte';
	import { goto } from '$app/navigation';
	import { showToast } from '$lib/utils/toast';
	import authFetch from '$lib/auth/authFetch';

	authMiddleware();

	let breadcrumbItems = [
		{ name: 'Dashboard', url: '/admin' },
		{ name: 'Users', url: '/admin/users' },
		{ name: 'Create', url: '', isActive: true }
	];

	let newUser: User = {
		name: '',
		email: '',
		password: '',
		password_confirmation: ''
	};

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let errors: { [key: string]: string[] } = {};
	let message = '';
	let status = false;
	let isLoading = false;
	interface User {
		name: string;
		email: string;
		password: string;
		password_confirmation: string;
	}

	async function createUser() {
		isLoading = true;
		try {
			const res = await authFetch(apiBaseURL + `/api/users`, {
				method: 'POST',
				body: JSON.stringify(newUser)
			});

			const responseBody = await res.json();

			if (!res.ok) {
				if (res.status === 422) {
					errors = responseBody.errors || {};
					message = responseBody.message || 'Validation failed.';
				} else {
					message = responseBody.message || 'Something went wrong.';
				}
				showToast(message, 'error');
				throw new Error(message);
			}

			status = responseBody.status || true;
			message = responseBody.message || 'User created successfully.';
			showToast(message, 'success');
			setTimeout(() => {
				goto('/admin/users');
			}, 1000);
		} catch (err) {
			console.error(err);
			message = 'Error fetching user data.';
			showToast(message, 'error');
		} finally {
			isLoading = false;
		}
	}
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="mx-auto max-w-lg pb-2 pt-6">
		<form on:submit|preventDefault={createUser} class="space-y-4">
			<div class="mb-3">
				<label class="form-label" for="name">Name</label>
				<input
					type="text"
					id="name"
					name="name"
					bind:value={newUser.name}
					placeholder="Enter name"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.name}
					<p class="text-sm text-red-500">{errors.name[0]}</p>
				{/if}
			</div>

			<div class="mb-3">
				<label class="form-label" for="email">Email</label>
				<input
					type="email"
					id="email"
					name="email"
					bind:value={newUser.email}
					placeholder="Enter email"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.email}
					<p class="text-sm text-red-500">{errors.email[0]}</p>
				{/if}
			</div>

			<div class="mb-3">
				<label class="form-label" for="password">Password</label>
				<input
					type="password"
					id="password"
					name="password"
					bind:value={newUser.password}
					placeholder="Enter password"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.password}
					<p class="text-sm text-red-500">{errors.password[0]}</p>
				{/if}
			</div>

			<div class="mb-3">
				<label class="form-label" for="password_confirmation">Confirm Password</label>
				<input
					type="password"
					id="password_confirmation"
					name="password_confirmation"
					bind:value={newUser.password_confirmation}
					placeholder="Confirm password"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.password_confirmation}
					<p class="text-sm text-red-500">{errors.password_confirmation[0]}</p>
				{/if}
			</div>

			<Button buttonType="submit" buttonLoading={isLoading} buttonText="Save" buttonColor="blue" />
		</form>
	</div>
</section>
