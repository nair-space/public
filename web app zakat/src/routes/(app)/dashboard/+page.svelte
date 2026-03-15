<script lang="ts">
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import { formatRupiah } from '$lib/utils/currency.js';

	// Placeholder summary data
	const summaryCards = [
		{ label: 'Total Zakat Fitrah (Uang)', value: formatRupiah(0), icon: '🌾', color: 'from-emerald-500/20 to-emerald-600/10' },
		{ label: 'Total Zakat Maal', value: formatRupiah(0), icon: '💰', color: 'from-blue-500/20 to-blue-600/10' },
		{ label: 'Total Fidyah', value: formatRupiah(0), icon: '🍚', color: 'from-orange-500/20 to-orange-600/10' },
		{ label: 'Total Infaq', value: formatRupiah(0), icon: '🤲', color: 'from-purple-500/20 to-purple-600/10' },
		{ label: 'Total Shodaqoh', value: formatRupiah(0), icon: '💝', color: 'from-pink-500/20 to-pink-600/10' },
		{ label: 'Total Pengeluaran', value: formatRupiah(0), icon: '📤', color: 'from-red-500/20 to-red-600/10' }
	];
</script>

<svelte:head>
	<title>Dashboard — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	<!-- Welcome Banner -->
	<GlassCard class="bg-gradient-to-r from-primary-600/20 to-primary-800/10">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-bold text-white">Selamat Datang! 👋</h2>
				<p class="mt-1 text-sm text-white/50">Ringkasan data pengelolaan Zakat hari ini</p>
			</div>
			<div class="text-4xl">📊</div>
		</div>
	</GlassCard>

	<!-- Summary Cards Grid -->
	<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
		{#each summaryCards as card, i (card.label)}
			<div class="animate-fade-in" style="animation-delay: {i * 80}ms">
				<GlassCard hover class="bg-gradient-to-br {card.color}">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-xs font-medium tracking-wider text-white/50 uppercase">{card.label}</p>
							<p class="mt-2 text-2xl font-bold text-white">{card.value}</p>
						</div>
						<span class="text-3xl">{card.icon}</span>
					</div>
				</GlassCard>
			</div>
		{/each}
	</div>

	<!-- Balance Overview -->
	<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
		<GlassCard title="Income vs Outcome">
			<div class="flex items-center justify-center py-12 text-white/30">
				<div class="text-center">
					<p class="text-4xl">📈</p>
					<p class="mt-2 text-sm">Chart akan ditampilkan di sini</p>
					<p class="text-xs text-white/20">Total Income - Total Outcome = Net Balance</p>
				</div>
			</div>
		</GlassCard>

		<GlassCard title="Transaksi Terbaru">
			<div class="flex items-center justify-center py-12 text-white/30">
				<div class="text-center">
					<p class="text-4xl">📋</p>
					<p class="mt-2 text-sm">Belum ada transaksi</p>
					<p class="text-xs text-white/20">Input data melalui modul di sidebar</p>
				</div>
			</div>
		</GlassCard>
	</div>
</div>
