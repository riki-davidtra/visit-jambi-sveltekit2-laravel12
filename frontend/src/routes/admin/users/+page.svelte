<script lang="ts">
	import { authMiddleware } from '$lib/middleware/authMiddleware';
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import Button from '$lib/components/Button.svelte';
	import Swal from 'sweetalert2';
	import 'sweetalert2/dist/sweetalert2.min.css';
	import { showToast } from '$lib/utils/toast';
	import authFetch from '$lib/auth/authFetch';

	authMiddleware();

	let breadcrumbItems = [
		{ name: 'Dashboard', url: '/admin' },
		{ name: 'Users', url: '', isActive: true }
	];

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let dataUsers: User[] = [];
	let message = '';
	let status = false;
	interface User {
		id: string;
		name: string;
		email: string;
	}

	onMount(() => {
		getUsers();
	});

	async function getUsers() {
		try {
			const response = await authFetch(`${apiBaseURL}/api/users`, {
				method: 'GET'
			});

			const result = await response.json();
			dataUsers = result.data;
		} catch (err) {
			console.error(err);
			message = 'Error fetching users.';
			showToast(message, 'error');
		}
	}

	async function deleteUser(userId: string) {
		Swal.fire({
			title: 'Are you sure?',
			text: 'Do you really want to delete this user?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
			cancelButtonText: 'No, cancel!'
		}).then(async (result) => {
			if (!result.isConfirmed) return;

			try {
				const response = await authFetch(`${apiBaseURL}/api/users/${userId}`, {
					method: 'DELETE'
				});

				if (!response.ok) {
					throw new Error(`Failed to fetch users: ${response.status}`);
				}

				dataUsers = dataUsers.filter((user) => user.id !== userId);

				const data = await response.json();
				status = data.status || true;
				message = data.message || 'User has been successfully deleted.';
				showToast(message, 'success');
			} catch (err) {
				console.error(err);
				message = 'Failed to delete the user. Please try again.';
				showToast(message, 'error');
			}
		});
	}

	function addUser() {
		goto('/admin/users/create');
	}

	function editUser(userId: string) {
		goto(`/admin/users/edit/${userId}`);
	}
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		<div class="mb-4 flex justify-end">
			<button
				on:click={() => addUser()}
				class="cursor-pointer rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
				>Add User</button
			>
		</div>

		<table class="mt-4 min-w-full table-auto border-collapse">
			<thead>
				<tr class="border-b">
					<th class="px-4 py-2 text-left">Name</th>
					<th class="px-4 py-2 text-left">Email</th>
					<th class="px-4 py-2 text-left">Actions</th>
				</tr>
			</thead>
			<tbody>
				{#each dataUsers as user}
					<tr class="border-b">
						<td class="px-4 py-2">{user.name}</td>
						<td class="px-4 py-2">{user.email}</td>
						<td class="px-4 py-2">
							<button
								on:click={() => editUser(user.id)}
								class="cursor-pointer rounded-lg bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500"
								>Edit</button
							>
							<button
								on:click={() => deleteUser(user.id)}
								class="cursor-pointer rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
								>Delete</button
							>
						</td>
					</tr>
				{/each}
			</tbody>
		</table>
	</div>
</section>
