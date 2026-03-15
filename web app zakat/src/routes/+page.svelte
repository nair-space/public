<script lang="ts">
	import { goto } from '$app/navigation';
	import { resolve } from '$app/paths';
	import { onMount } from 'svelte';

	onMount(() => {
		goto(resolve('/login'));
	});
</script>

<div class="flex min-h-screen items-center justify-center">
	<p class="text-white/40">Redirecting...</p>
</div>
