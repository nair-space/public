<script lang="ts">
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import type { PageData } from './$types';
	import { formatRupiah } from '$lib/utils/currency';

	let { data }: { data: PageData } = $props();

	const categories = $derived.by(() => [
		{
			name: 'Zakat Fitrah (Uang)',
			income: data.totals.fitrahUang.income,
			outcome: data.totals.fitrahUang.outcome
		},
		{
			name: 'Zakat Fitrah (Beras)',
			income: data.totals.fitrahBeras.income,
			outcome: data.totals.fitrahBeras.outcome,
			isBeras: true
		},
		{
			name: 'Zakat Maal',
			income: data.totals.maalUang.income,
			outcome: data.totals.maalUang.outcome
		},
		{
			name: 'Fidyah (Uang)',
			income: data.totals.fidyahUang.income,
			outcome: data.totals.fidyahUang.outcome
		},
		{
			name: 'Fidyah (Beras)',
			income: data.totals.fidyahBeras.income,
			outcome: data.totals.fidyahBeras.outcome,
			isBeras: true
		},
		{
			name: 'Infaq',
			income: data.totals.infaqUang.income,
			outcome: data.totals.infaqUang.outcome
		},
		{
			name: 'Shodaqoh',
			income: data.totals.shodaqohUang.income,
			outcome: data.totals.shodaqohUang.outcome
		}
	]);

	function formatDate(date: Date | string | null) {
		if (!date) return '-';
		return new Date(date).toLocaleDateString('id-ID', {
			day: '2-digit',
			month: 'short',
			year: 'numeric',
			hour: '2-digit',
			minute: '2-digit'
		});
	}

	type TransactionList = {
		incomeUang: {
			label: string;
			amount: number;
			createdAt: Date;
			idNotaInput: number | null;
		}[];
		incomeBeras: {
			label: string;
			amount: number;
			createdAt: Date;
			idNotaInput: number | null;
		}[];
		outcomeUang: {
			label: string;
			amount: number;
			createdAt: Date;
			idNotaInput: number | null;
		}[];
		outcomeBeras: {
			label: string;
			amount: number;
			createdAt: Date;
			idNotaInput: number | null;
		}[];
	};

	function buildTransactionColumns(transactions: TransactionList) {
		return [
			{
				title: 'Semua Transaksi Pemasukan Uang',
				items: transactions.incomeUang,
				isBeras: false
			},
			{
				title: 'Semua Transaksi Pemasukan Beras',
				items: transactions.incomeBeras,
				isBeras: true
			},
			{
				title: 'Semua Transaksi Pengeluaran Uang',
				items: transactions.outcomeUang,
				isBeras: false
			},
			{
				title: 'Semua Transaksi Pengeluaran Beras',
				items: transactions.outcomeBeras,
				isBeras: true
			}
		];
	}
</script>

<svelte:head>
	<title>Lihat Laporan — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	<GlassCard title="Ringkasan Neraca Pengeluaran dan Pemasukan">
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead>
					<tr class="border-b border-white/10 text-left text-white/50">
						<th class="px-4 py-3 font-medium">Kategori</th>
						<th class="px-4 py-3 text-right font-medium">Total Income</th>
						<th class="px-4 py-3 text-right font-medium">Total Outcome</th>
						<th class="px-4 py-3 text-right font-medium">Net Balance</th>
					</tr>
				</thead>
				<tbody>
					{#each categories as cat (cat.name)}
						<tr class="border-b border-white/5 transition-colors hover:bg-white/5">
							<td class="px-4 py-3 text-white/80">{cat.name}</td>
							<td class="px-4 py-3 text-right text-emerald-400">
								{cat.isBeras ? `${cat.income.toFixed(2)} kg` : formatRupiah(cat.income)}
							</td>
							<td class="px-4 py-3 text-right text-red-400">
								{cat.isBeras ? `${cat.outcome.toFixed(2)} kg` : formatRupiah(cat.outcome)}
							</td>
							<td class="px-4 py-3 text-right font-semibold text-white">
								{cat.isBeras
									? `${(cat.income - cat.outcome).toFixed(2)} kg`
									: formatRupiah(cat.income - cat.outcome)}
							</td>
						</tr>
					{/each}
				</tbody>
			</table>
		</div>
	</GlassCard>

		<GlassCard title="Semua Transaksi">
		{#await data.transactions}
			<div class="p-4 text-sm text-white/50">Memuat transaksi...</div>
		{:then transactions}
			<div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2 xl:grid-cols-4">
				{#each buildTransactionColumns(transactions) as column (column.title)}
					<div class="rounded-lg border border-white/10 bg-white/5 p-3">
						<h3 class="mb-3 text-sm font-semibold text-white/80">{column.title}</h3>
						{#if column.items.length === 0}
							<p class="text-xs text-white/40">Belum ada transaksi.</p>
						{:else}
							<div class="max-h-96 space-y-2 overflow-auto pr-1">
								{#each column.items as item}
									<div class="rounded-md border border-white/10 bg-black/20 p-2">
										<div class="text-xs text-white/70">{item.label}</div>
										<div class="text-sm font-semibold text-white">
											{#if column.isBeras}
												{item.amount.toFixed(2)} kg
											{:else}
												{formatRupiah(item.amount)}
											{/if}
										</div>
										<div class="text-[11px] text-white/50">
											#{item.idNotaInput ?? '-'} • {formatDate(item.createdAt)}
										</div>
									</div>
								{/each}
							</div>
						{/if}
					</div>
				{/each}
			</div>
		{:catch}
			<div class="p-4 text-sm text-red-300">Gagal memuat transaksi.</div>
		{/await}
	</GlassCard>
</div>

