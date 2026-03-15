<script lang="ts">
	import Sidebar from '$lib/components/layout/Sidebar.svelte';
	import Topbar from '$lib/components/layout/Topbar.svelte';
import type { Snippet } from 'svelte';
	import type { LayoutData } from './$types';

	const { children, data }: { children: Snippet; data: LayoutData } = $props();

	let sidebarCollapsed = $state(false);
	let mobileMenuOpen = $state(false);
</script>

<div class="flex min-h-screen">
	<Sidebar
		userRole={data.user.role}
		mobileOpen={mobileMenuOpen}
		onRequestClose={() => (mobileMenuOpen = false)}
		onCollapseChange={(value) => (sidebarCollapsed = value)}
	/>

	{#if mobileMenuOpen}
		<button
			class="fixed inset-0 z-30 bg-black/60 md:hidden"
			aria-label="Tutup menu"
			onclick={() => (mobileMenuOpen = false)}
		></button>
	{/if}

	<div
		class={`flex flex-1 flex-col transition-all duration-300 ml-0 ${sidebarCollapsed ? 'md:ml-20' : 'md:ml-64'}`}
	>
		<Topbar sidebarCollapsed={sidebarCollapsed} onMenuToggle={() => (mobileMenuOpen = !mobileMenuOpen)} />

		<main class="flex-1 p-4 md:p-6">
			<div class="animate-fade-in">
				{@render children()}
			</div>
		</main>
	</div>
</div>
