<script lang="ts">
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	import Pagination from '$lib/components/Pagination.svelte';

	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'Destinations', url: '', isActive: true }
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
	let destinations: Destination[] = [];
	let currentPage = 1;
	let limit = 5;
	let totalPages = 1;
	let apiError = '';

	async function getDestinations(page: number = 1) {
		try {
			const res = await fetch(`${apiBaseURL}/api/destinations?page=${page}&limit=${limit}`, {
				method: 'GET'
			});
			if (!res.ok) {
				throw new Error(`Failed to fetch: ${res.status}`);
			}
			const result = await res.json();
			destinations = result.data;
			currentPage = result.meta.current_page;
			totalPages = result.meta.last_page;
		} catch (err) {
			apiError = 'Error fetching destinations.';
			console.error(err);
		}
	}

	getDestinations();

	function changePage(page: number) {
		if (page > 0 && page <= totalPages) {
			getDestinations(page);
		}
	}
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		{#if apiError}
			<p class="text-red-500">{apiError}</p>
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

			<div class="mt-6 flex items-center justify-center">
				<Pagination {currentPage} {totalPages} {changePage} />
			</div>
		{/if}
	</div>
</section>
