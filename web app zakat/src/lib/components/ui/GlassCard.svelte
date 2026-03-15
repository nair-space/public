<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		title?: string;
		class?: string;
		hover?: boolean;
		children: Snippet;
	}

	let { title, class: className = '', hover = false, children }: Props = $props();
</script>

<div class="glass-card {hover ? 'glass-card-hover' : ''} p-6 {className}">
	{#if title}
		<h3 class="mb-4 text-lg font-semibold text-white/90">{title}</h3>
	{/if}
	{@render children()}
</div>
