<script lang="ts">
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';
	let breadcrumbItems = [
		{ name: 'Home', url: '/' },
		{ name: 'About', url: '', isActive: true }
	];
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		<!-- content -->
	</div>
</section>
