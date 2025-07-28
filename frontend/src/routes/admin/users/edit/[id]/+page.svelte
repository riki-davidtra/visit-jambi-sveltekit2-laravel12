<script lang="ts">
	import { authMiddleware } from '$lib/middleware/authMiddleware';
	import { onMount } from 'svelte';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import Button from '$lib/components/Button.svelte';
	import { goto } from '$app/navigation';
	import { page } from '$app/stores';
	import { showToast } from '$lib/utils/toast';
	import authFetch from '$lib/auth/authFetch';

	authMiddleware();

	let breadcrumbItems = [
		{ name: 'Dashboard', url: '/admin' },
		{ name: 'Users', url: '/admin/users' },
		{ name: 'Edit', url: '', isActive: true }
	];

	let user: User = {
		name: '',
		email: '',
		password: '',
		password_confirmation: ''
	};

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	$: id = $page.params.id;
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

	async function updateUser() {
		const token = localStorage.getItem('token');

		if (!token) {
			message = 'You are not authenticated. Please log in.';
			showToast(message, 'error');
			return;
		}

		isLoading = true;
		try {
			const response = await authFetch(apiBaseURL + `/api/users/${id}`, {
				method: 'PUT',
				body: JSON.stringify(user)
			});

			const responseBody = await response.json();

			if (!response.ok) {
				if (response.status === 422) {
					errors = responseBody.errors || {};
					message = responseBody.message || 'Validation failed.';
				} else {
					message = responseBody.message || 'Something went wrong.';
				}
				showToast(message, 'error');
				throw new Error(message);
			}

			status = responseBody.status || true;
			message = responseBody.message || 'User updated successfully.';
			showToast(message, 'success');
			setTimeout(() => {
				goto('/admin/users');
			}, 1000);
		} catch (err) {
			console.error(err);
			message = 'Failed to update user.';
			showToast(message, 'error');
		} finally {
			isLoading = false;
		}
	}

	onMount(async () => {
		const token = localStorage.getItem('token');

		if (!token) {
			message = 'You are not authenticated. Please log in.';
			showToast(message, 'error');
			return;
		}

		try {
			const response = await authFetch(apiBaseURL + `/api/users/${id}`, {
				method: 'GET'
			});

			const responseBody = await response.json();

			if (!response.ok) {
				const message = responseBody.message || 'Failed to load user data.';
				showToast(message, 'error');
				throw new Error(message);
			}

			user = responseBody.data.user;
		} catch (err) {
			console.error(err);
			message = 'Error fetching user data.';
			showToast(message, 'error');
		}
	});
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="mx-auto max-w-lg pb-2 pt-6">
		<form on:submit|preventDefault={updateUser} class="space-y-4">
			<div class="mb-3">
				<label class="form-label" for="name">Name</label>
				<input
					type="text"
					id="name"
					name="name"
					bind:value={user.name}
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
					bind:value={user.email}
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
					bind:value={user.password}
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
					bind:value={user.password_confirmation}
					placeholder="Confirm password"
					class="form-input w-full rounded-md border border-gray-300 px-4 py-2"
				/>
				{#if errors.password_confirmation}
					<p class="text-sm text-red-500">{errors.password_confirmation[0]}</p>
				{/if}
			</div>

			<Button
				buttonType="submit"
				buttonLoading={isLoading}
				buttonText="Update"
				buttonColor="blue"
			/>
		</form>
	</div>
</section>
