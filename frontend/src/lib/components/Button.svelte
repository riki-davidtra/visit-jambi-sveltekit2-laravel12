<script lang="ts">
	export let buttonType: 'button' | 'submit' | 'reset' | null = 'button';
	export let buttonLoading: boolean = false;
	export let loadingText: string = 'Loading...';
	export let buttonText: string = 'Submit';
	export let buttonColor: string = 'blue';
</script>

<button
	type={buttonType}
	class={`mt-4 cursor-pointer rounded-lg bg-${buttonColor}-500 px-4 py-2 text-white hover:bg-${buttonColor}-600 focus:outline-none focus:ring-2 focus:ring-${buttonColor}-500`}
	disabled={buttonLoading}
>
	{#if buttonLoading}
		{loadingText}
	{:else}
		{buttonText}
	{/if}
</button>
