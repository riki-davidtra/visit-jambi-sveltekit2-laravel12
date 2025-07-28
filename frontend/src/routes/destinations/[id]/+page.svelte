<script lang="ts">
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import { onMount } from 'svelte';
	import { page } from '$app/stores';

	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'Destinations', url: '/destinations' },
		{ name: 'Detail', url: '', isActive: true }
	];

	type Category = {
		name: string;
	};

	type Destination = {
		id: number;
		name: string;
		description: string;
		image: string;
		category: Category;
		created_at: string;
	};

	const apiBaseURL = import.meta.env.VITE_API_BASE_URL;
	let { id } = $page.params;
	let destination: Destination | null = null;
	let isLoading = true;
	let error: string | null = null;

	async function fetchDestination(id: number) {
		try {
			const response = await fetch(`${apiBaseURL}/api/destinations/${id}`);
			if (!response.ok) {
				throw new Error('Failed to fetch destination data.');
			}
			const result = await response.json();
			destination = result.data;
		} catch (err) {
			if (err instanceof Error) {
				error = err.message;
			} else {
				error = 'An unknown error occurred.';
			}
		} finally {
			isLoading = false;
		}
	}

	onMount(() => {
		fetchDestination(Number(id));
	});
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		{#if isLoading}
			<p>Loading...</p>
		{:else if error}
			<p>Error: {error}</p>
		{:else if destination}
			<div class="overflow-hidden rounded-lg bg-white shadow-lg">
				<img
					src={apiBaseURL + '/storage/' + destination.image}
					alt={destination.name}
					class="h-96 w-full object-cover"
				/>
				<div class="p-6">
					<h5 class="mb-2 text-xl font-bold">{destination.name}</h5>
					<div class="mb-2 flex items-center gap-6 text-sm">
						<p class="text-sm text-gray-600">
							{new Date(destination.created_at).toLocaleDateString()}
						</p>
						<p class="text-sm text-gray-600">{destination.category?.name}</p>
					</div>
					<p class="mb-4 text-base text-gray-700">{@html destination.description}</p>
				</div>
			</div>
		{/if}
	</div>
</section>
