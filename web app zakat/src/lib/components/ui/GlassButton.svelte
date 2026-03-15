<script lang="ts">
	import type { Snippet } from 'svelte';

	interface Props {
		type?: 'button' | 'submit' | 'reset';
		variant?: 'primary' | 'outline' | 'danger';
		size?: 'sm' | 'md';
		loading?: boolean;
		disabled?: boolean;
		class?: string;
		onclick?: () => void;
		children: Snippet;
	}

	let {
		type = 'button',
		variant = 'primary',
		size = 'md',
		loading = false,
		disabled = false,
		class: className = '',
		onclick,
		children
	}: Props = $props();
</script>

<button
	{type}
	{onclick}
	class="glass-button {variant === 'outline' ? 'glass-button-outline' : ''} {variant === 'danger' ? 'glass-button-danger' : ''} {size === 'sm' ? 'px-2 py-1 text-sm' : 'px-4 py-2'} {className}"
	disabled={disabled || loading}
>
	{#if loading}
		<svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
			<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25" />
			<path
				fill="currentColor"
				d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
				class="opacity-75"
			/>
		</svg>
	{/if}
	{@render children()}
</button>
