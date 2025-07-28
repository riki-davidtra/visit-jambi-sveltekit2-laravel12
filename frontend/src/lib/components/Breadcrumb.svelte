<script lang="ts">
	export let breadcrumbItems: { name: string; url: string; isActive?: boolean }[] = [];
</script>

<nav class="flex" aria-label="Breadcrumb">
	<ol class="inline-flex items-center space-x-1 md:space-x-3">
		{#each breadcrumbItems as { name, url, isActive }, index}
			<li class="inline-flex items-center">
				{#if index === 0}
					{#if isActive}
						<span class="ml-1 text-base font-medium text-sky-400 md:ml-2">{name}</span>
					{:else}
						<a
							href={url}
							class="inline-flex items-center text-base font-medium text-gray-700 hover:text-sky-400"
						>
							{name}
						</a>
					{/if}
				{/if}

				{#if index !== 0}
					<div class="flex items-center">
						<svg
							class="text-gray-400"
							fill="currentColor"
							stroke="currentColor"
							width="10px"
							height="10px"
							viewBox="-1.92 -1.92 35.84 35.84"
							version="1.1"
							xmlns="http://www.w3.org/2000/svg"
							stroke-width="3.2"
							><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g
								id="SVGRepo_tracerCarrier"
								stroke-linecap="round"
								stroke-linejoin="round"
							></g><g id="SVGRepo_iconCarrier">
								<path
									d="M8.489 31.975c-0.271 0-0.549-0.107-0.757-0.316-0.417-0.417-0.417-1.098 0-1.515l14.258-14.264-14.050-14.050c-0.417-0.417-0.417-1.098 0-1.515s1.098-0.417 1.515 0l14.807 14.807c0.417 0.417 0.417 1.098 0 1.515l-15.015 15.022c-0.208 0.208-0.486 0.316-0.757 0.316z"
								></path>
							</g></svg
						>

						{#if isActive}
							<span class="ml-1 text-base font-medium text-sky-400 md:ml-2">{name}</span>
						{:else}
							<a
								href={url}
								class="ml-1 text-base font-medium text-gray-700 hover:text-sky-400 md:ml-2"
								>{name}</a
							>
						{/if}
					</div>
				{/if}
			</li>
		{/each}
	</ol>
</nav>
