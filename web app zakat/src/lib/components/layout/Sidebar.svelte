<script lang="ts">
	import { page } from '$app/state';
	import { resolve } from '$app/paths';

	interface Props {
		userRole?: string;
		mobileOpen?: boolean;
		onRequestClose?: () => void;
		onCollapseChange?: (value: boolean) => void;
	}

	let {
		userRole = 'amil',
		mobileOpen = false,
		onRequestClose = () => {},
		onCollapseChange = () => {}
	}: Props = $props();

	// Debug logging
	$effect(() => {
		console.log('Sidebar userRole:', userRole);
	});

	interface MenuItem {
		label: string;
		href: string;
		icon: string;
		adminOnly?: boolean;
	}

	let collapsed = $state(false);

	const menuItems: MenuItem[] = [
		{ label: 'Dashboard', href: '/dashboard', icon: '📊' },
		{ label: 'Cara Hitung', href: '/cara-hitung', icon: '🧮' },
		{ label: 'Zakat Fitrah', href: '/zakat-fitrah', icon: '🌾' },
		{ label: 'Zakat Maal', href: '/zakat-maal', icon: '💰' },
		{ label: 'Fidyah', href: '/fidyah', icon: '🍚' },
		{ label: 'Infaq', href: '/infaq', icon: '🤲' },
		{ label: 'Shodaqoh', href: '/shodaqoh', icon: '💝' },
		{ label: 'Pengeluaran', href: '/pengeluaran', icon: '📤' },
		{ label: 'Amil & Volunteer', href: '/amil-volunteer', icon: '👥' },
		{ label: 'Lihat Laporan', href: '/lihat-laporan', icon: '📋' },
		{ label: 'Import/Export', href: '/import-export', icon: '📁' },
		{ label: 'Cetak Laporan', href: '/cetak-laporan', icon: '🖨️' },
		{ label: 'Tambah Akun Amil', href: '/tambah-akun-amil', icon: '👤', adminOnly: true }
	];

	// Filter menu items based on user role
	const visibleMenuItems = $derived(
		menuItems.filter(item => !item.adminOnly || userRole === 'admin')
	);

	function isActive(href: string): boolean {
		return page.url.pathname === href || page.url.pathname.startsWith(href + '/');
	}
</script>

<aside
	class="glass-sidebar fixed top-0 left-0 z-40 flex h-screen flex-col transition-all duration-300 md:translate-x-0"
	class:w-64={!collapsed}
	class:w-20={collapsed}
	class:translate-x-0={mobileOpen}
	class:-translate-x-full={!mobileOpen}
>
	<!-- Logo -->
	<div class="flex h-16 items-center gap-3 border-b border-white/10 px-5">
		<button
			class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg text-white/70 hover:bg-white/10 md:hidden"
			aria-label="Tutup menu"
			onclick={onRequestClose}
		>
			<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
			</svg>
		</button>
		<div
			class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-primary-500 to-primary-700 text-lg font-bold text-white shadow-lg"
		>
			Z
		</div>
		{#if !collapsed}
			<div class="animate-fade-in overflow-hidden">
				<h1 class="text-base font-bold tracking-tight text-white">Zakat App</h1>
				<p class="text-[10px] tracking-widest text-white/40 uppercase">Management System</p>
			</div>
		{/if}
	</div>

	<!-- Menu -->
	<nav class="flex-1 overflow-y-auto px-3 py-4">
		<ul class="flex flex-col gap-1">
			{#each visibleMenuItems as item (item.href)}
				<li>
					<a
						href={resolve(item.href as '/dashboard')}
						class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
							{isActive(item.href)
							? 'bg-primary-600/30 text-primary-300 shadow-lg shadow-primary-900/20 border border-primary-500/20'
							: 'text-white/60 hover:bg-white/8 hover:text-white/90 border border-transparent'}"
						title={collapsed ? item.label : undefined}
					>
						<span class="shrink-0 text-lg">{item.icon}</span>
						{#if !collapsed}
							<span class="animate-fade-in truncate">{item.label}</span>
						{/if}
						{#if isActive(item.href) && !collapsed}
							<span class="ml-auto h-1.5 w-1.5 rounded-full bg-primary-400 shadow-sm shadow-primary-400/50"
							></span>
						{/if}
					</a>
				</li>
			{/each}
		</ul>
	</nav>

	<!-- Collapse Toggle -->
	<div class="border-t border-white/10 p-3">
		<button
			onclick={() => {
				collapsed = !collapsed;
				onCollapseChange(collapsed);
			}}
			class="hidden w-full items-center justify-center rounded-xl py-2.5 text-white/40 transition-all hover:bg-white/8 hover:text-white/70 md:flex"
			title={collapsed ? 'Expand sidebar' : 'Collapse sidebar'}
		>
			<svg
				class="h-5 w-5 transition-transform duration-300"
				class:rotate-180={collapsed}
				fill="none"
				viewBox="0 0 24 24"
				stroke="currentColor"
				stroke-width="2"
			>
				<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
			</svg>
		</button>
	</div>
</aside>
