<script lang="ts">
	interface Props {
		label?: string;
		type?: string;
		name: string;
		placeholder?: string;
		value?: string;
		error?: string;
		required?: boolean;
		readonly?: boolean;
		size?: 'sm' | 'md';
		class?: string;
		oninput?: (e: Event) => void;
	}

	let {
		label,
		type = 'text',
		name,
		placeholder = '',
		value = $bindable(''),
		error,
		required = false,
		readonly = false,
		size = 'md',
		class: className = '',
		oninput
	}: Props = $props();
</script>

<div class="flex flex-col gap-1.5 {className}">
	{#if label}
		<label for={name} class="text-sm font-medium text-white/70">
			{label}
			{#if required}<span class="text-red-400">*</span>{/if}
		</label>
	{/if}
	<input
		{type}
		{name}
		id={name}
		{placeholder}
		{required}
		{readonly}
		bind:value
		{oninput}
		class="glass-input {size === 'sm' ? 'px-2 py-1 text-sm' : ''}"
		class:border-red-400={error}
	/>
	{#if error}
		<p class="text-xs text-red-400">{error}</p>
	{/if}
</div>
