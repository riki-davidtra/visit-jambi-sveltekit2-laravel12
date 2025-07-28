<script lang="ts">
	import { onMount } from 'svelte';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import SubmitButton from '$lib/components/Button.svelte';
	import Swal from 'sweetalert2';
	import 'sweetalert2/dist/sweetalert2.min.css';

	let breadcrumbItems = [{ name: 'Home', url: '/', isActive: true }];

	type Category = {
		name: string;
	};

	type Destination = {
		id: number;
		name: string;
		description: string;
		image: string;
		created_at: string;
		category: Category;
	};

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let destinations: Destination[] = [];
	let limit = 6;
	let name = '';
	let email = '';
	let message = '';
	let isLoading = false;
	let notifMessage = '';
	let fetchMessages: { name: string; email: string; message: string }[] = [];

	function showToast(message: string, icon: 'success' | 'error') {
		Swal.fire({
			toast: true,
			position: 'top-end',
			icon: icon,
			title: message,
			showConfirmButton: false,
			showCloseButton: true,
			timer: 3000,
			timerProgressBar: true
		});
	}

	async function getDestinations() {
		try {
			const res = await fetch(apiBaseURL + `/api/destinations?limit=${limit}`, {
				method: 'GET'
			});
			if (!res.ok) {
				throw new Error(`Failed to fetch: ${res.status}`);
			}
			const result = await res.json();
			destinations = result.data;
		} catch (err) {
			message = 'Error fetching destinations.';
			console.error(err);
		}
	}

	async function postMessage() {
		if (!name || !email || !message) {
			notifMessage = 'Please fill in all fields!';
			showToast(notifMessage, 'error');
			return;
		}

		const formData = {
			name,
			email,
			message
		};

		isLoading = true;

		try {
			const res = await fetch(apiBaseURL + `/api/messages`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(formData)
			});
			if (!res.ok) {
				throw new Error(`Failed to send message: ${res.status}`);
			}
			const result = await res.json();
			notifMessage = 'Message sent successfully!';
			fetchMessages = [{ name, email, message }, ...fetchMessages];
			name = '';
			email = '';
			message = '';
			showToast(notifMessage, 'success');
		} catch (err) {
			console.error(err);
			notifMessage = 'Error sending message. Please try again.';
			showToast(notifMessage, 'error');
		} finally {
			isLoading = false;
		}
	}

	async function getMessages() {
		try {
			const res = await fetch(apiBaseURL + `/api/messages`, {
				method: 'GET'
			});

			if (!res.ok) {
				notifMessage = `Failed to fetch messages: ${res.status}`;
				showToast(notifMessage, 'error');
				throw new Error(notifMessage);
			}

			const result = await res.json();
			fetchMessages = result.data;
		} catch (err) {
			console.error(err);
			notifMessage = 'Error fetching messages.';
			showToast(notifMessage, 'error');
		}
	}

	onMount(() => {
		getDestinations();
		getMessages();
	});
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		{#if message}
			<p class="text-red-500">{message}</p>
		{:else if destinations.length === 0}
			<p class="text-center text-gray-500">Loading...</p>
		{:else}
			<div class="flex items-center justify-center">
				<div class="grid w-full grid-cols-1 gap-4 md:grid-cols-3">
					{#each destinations as destination}
						<a href={`/destinations/${destination.id}`} class="text-sm text-gray-600">
							<div class="flex h-full flex-col rounded-lg bg-gray-100 p-4 text-center shadow-lg">
								<img
									src={apiBaseURL + '/storage/' + destination.image}
									alt={destination.name}
									class="h-54 mb-2 w-full rounded-md object-cover"
								/>
								<h3 class="text-lg font-semibold">{destination.name}</h3>
								<div class="flex items-center justify-center gap-6 text-sm">
									<p class="text-sm text-gray-600">
										{new Date(destination.created_at).toLocaleDateString()}
									</p>
									<p class="text-sm text-gray-600">{destination.category?.name}</p>
								</div>
								<p class="line-clamp-3 text-sm text-gray-600">{@html destination.description}</p>
							</div>
						</a>
					{/each}
				</div>
			</div>

			<div class="mt-4 flex items-center justify-center">
				<a
					href="/destinations"
					class="rounded-lg bg-sky-600 px-4 py-2 text-base text-white hover:bg-sky-700"
					>View all destinations</a
				>
			</div>
		{/if}
	</div>

	<div class="mx-auto max-w-2xl pb-2 pt-6">
		<h3 class="mb-4 text-center text-xl font-bold text-gray-800">Contact Us</h3>

		<form on:submit|preventDefault={postMessage}>
			<div class="mb-4 flex flex-col gap-2">
				<label for="name" class="font-medium text-gray-700">Name</label>
				<input
					type="text"
					name="name"
					id="name"
					bind:value={name}
					placeholder="Enter Name"
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
				/>
			</div>
			<div class="mb-4 flex flex-col gap-2">
				<label for="email" class="font-medium text-gray-700">Email</label>
				<input
					type="email"
					name="email"
					id="email"
					bind:value={email}
					placeholder="Enter Email"
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
				/>
			</div>
			<div class="mb-4 flex flex-col gap-2">
				<label for="message" class="font-medium text-gray-700">Message</label>
				<textarea
					name="message"
					id="message"
					bind:value={message}
					placeholder="Enter Message"
					class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
				></textarea>
			</div>
			<div class="mt-6">
				<SubmitButton buttonLoading={isLoading} buttonText="Send Message" buttonColor="blue" />
			</div>
		</form>

		<!-- Displaying Sent Messages in a Table -->
		<h3 class="mt-8 text-center text-xl font-bold text-gray-800">Sent Messages</h3>
		<table class="mt-4 min-w-full table-auto border-collapse">
			<thead>
				<tr class="border-b">
					<th class="px-4 py-2 text-left">Name</th>
					<th class="px-4 py-2 text-left">Email</th>
					<th class="px-4 py-2 text-left">Message</th>
				</tr>
			</thead>
			<tbody>
				{#each fetchMessages as user}
					<tr class="border-b">
						<td class="px-4 py-2">{user.name}</td>
						<td class="px-4 py-2">{user.email}</td>
						<td class="px-4 py-2">{user.message}</td>
					</tr>
				{/each}
			</tbody>
		</table>
	</div>
</section>
