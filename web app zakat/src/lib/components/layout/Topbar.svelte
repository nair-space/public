<script lang="ts">
	import { page } from '$app/state';
	import { authClient } from '$lib/auth-client';
	import { goto } from '$app/navigation';
	import { resolve } from '$app/paths';

	interface Props {
		sidebarCollapsed?: boolean;
		onMenuToggle?: () => void;
	}

	let { sidebarCollapsed = false, onMenuToggle = () => {} }: Props = $props();

	// Derive page title from pathname
	const pageTitles: Record<string, string> = {
		'/dashboard': 'Dashboard',
		'/cara-hitung': 'Cara Hitung',
		'/zakat-fitrah': 'Zakat Fitrah',
		'/zakat-maal': 'Zakat Maal',
		'/fidyah': 'Fidyah',
		'/infaq': 'Infaq',
		'/shodaqoh': 'Shodaqoh',
		'/pengeluaran': 'Pengeluaran',
		'/amil-volunteer': 'Amil & Volunteer',
		'/lihat-laporan': 'Lihat Laporan',
		'/import-export': 'Import / Export',
		'/cetak-laporan': 'Cetak Laporan'
	};

	let pageTitle = $derived(pageTitles[page.url.pathname] ?? 'Zakat App');

	async function handleLogout() {
		await authClient.signOut();
		goto(resolve('/login'));
	}
</script>

<header
	class="glass-card sticky top-0 z-30 flex h-16 items-center justify-between border-b border-white/10 px-4 md:px-6 transition-all duration-300"
	style="border-radius: 0;"
>
	<div class="flex items-center gap-3">
		<button
			class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-white/70 hover:bg-white/10 md:hidden"
			aria-label="Buka menu"
			onclick={onMenuToggle}
		>
			<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
			</svg>
		</button>
		<h2 class="text-lg font-semibold text-white/90">{pageTitle}</h2>
	</div>

	<div class="flex items-center gap-3 md:gap-4">
		<!-- Current date -->
		<span class="hidden text-sm text-white/40 sm:inline">
			{new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
		</span>

		<!-- Logout button -->
		<button
			onclick={handleLogout}
			class="flex items-center gap-2 rounded-xl border border-white/10 px-4 py-2 text-sm text-white/60 transition-all hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-400"
		>
			<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
				<path
					stroke-linecap="round"
					stroke-linejoin="round"
					d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
				/>
			</svg>
			<span class="hidden sm:inline">Logout</span>
		</button>
	</div>
</header>
