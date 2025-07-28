<script lang="ts">
	import '../../app.css';
	import Navbar from '$lib/components/Navbar.svelte';
	import Footer from '$lib/components/Footer.svelte';

	// let { children } = $props();
</script>

<div class="flex min-h-screen flex-col justify-between">
	<!-- <Navbar /> -->

	<div class="flex-grow px-4 py-8 md:px-24">
		<!-- {@render children()} -->
		<slot />
	</div>

	<Footer />
</div>
