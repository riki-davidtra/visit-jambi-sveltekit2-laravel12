<script lang="ts">
	export let currentPage: number;
	export let totalPages: number;
	export let changePage: (page: number) => void;

	function getPaginationNumbers() {
		const pages = [];
		const delta = 2;

		pages.push(1);

		for (
			let i = Math.max(2, currentPage - delta);
			i <= Math.min(totalPages - 1, currentPage + delta);
			i++
		) {
			pages.push(i);
		}

		if (totalPages > 1) {
			pages.push(totalPages);
		}

		return pages;
	}
</script>

<button
	on:click={() => changePage(currentPage - 1)}
	disabled={currentPage === 1}
	class="cursor-pointer rounded-lg bg-sky-600 px-4 py-1 text-sm text-white hover:bg-sky-700 disabled:cursor-not-allowed"
>
	Previous
</button>
<div class="mx-4">
	{#each getPaginationNumbers() as page, i}
		{#if i > 0 && getPaginationNumbers()[i] !== getPaginationNumbers()[i - 1] + 1}
			<span class="mx-1">...</span>
		{/if}

		<button
			on:click={() => changePage(page)}
			class="cursor-pointer rounded-lg px-4 py-1 text-sm hover:bg-sky-700 {currentPage === page
				? 'bg-sky-600 text-white'
				: 'bg-gray-200 text-gray-600 hover:text-white'}"
			disabled={currentPage === page}
		>
			{page}
		</button>
	{/each}
</div>
<button
	on:click={() => changePage(currentPage + 1)}
	disabled={currentPage === totalPages}
	class="cursor-pointer rounded-lg bg-sky-600 px-4 py-1 text-sm text-white hover:bg-sky-700 disabled:cursor-not-allowed"
>
	Next
</button>
