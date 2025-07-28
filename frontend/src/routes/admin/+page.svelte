<script lang="ts">
	import { authMiddleware } from '$lib/middleware/authMiddleware';
	import Breadcrumb from '$lib/components/Breadcrumb.svelte';

	authMiddleware();

	let breadcrumbItems = [{ name: 'Dashboard', url: '', isActive: true }];
</script>

<section>
	<Breadcrumb {breadcrumbItems} />

	<div class="pb-2 pt-6">
		<!-- content -->
	</div>
</section>
