<script lang="ts">
	import { enhance } from '$app/forms';
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import GlassButton from '$lib/components/ui/GlassButton.svelte';
	import type { ActionData } from './$types';
	import { formatRupiah } from '$lib/utils/currency';
	import { jsPDF } from 'jspdf';

	type Totals = {
		income: {
			fitrahUang: number;
			fitrahBeras: number;
			maalUang: number;
			fidyahUang: number;
			fidyahBeras: number;
			infaqUang: number;
			shodaqohUang: number;
		};
		outcome: {
			fitrahUang: number;
			fitrahBeras: number;
			maalUang: number;
			fidyahUang: number;
			fidyahBeras: number;
			infaqUang: number;
			shodaqohUang: number;
		};
	};

	type ReportData = {
		range: { from: string; to: string };
		totals: Totals;
	};

	let { form }: { form: ActionData } = $props();
	let reportData: ReportData | null = $state(null);

	let includeFitrah = $state(true);
	let includeMaal = $state(true);
	let includeFidyah = $state(true);
	let includeInfaq = $state(true);
	let includeShodaqoh = $state(true);
	let includePengeluaran = $state(true);

	$effect(() => {
		if (form?.success && form?.totals && form?.range) {
			reportData = {
				range: form.range,
				totals: form.totals
			};
		}
	});

	function moneyLine(label: string, value: number): string {
		return `${label}: ${formatRupiah(value)}`;
	}

	function berasLine(label: string, value: number): string {
		return `${label}: ${value.toFixed(2)} kg`;
	}

	function buildLines() {
		if (!reportData) {
			return { income: [], outcome: [], net: [] } as { income: string[]; outcome: string[]; net: string[] };
		}

		const income: string[] = [];
		const outcome: string[] = [];
		const net: string[] = [];

		const t = reportData.totals;

		if (includeFitrah) {
			income.push(moneyLine('Zakat Fitrah Uang', t.income.fitrahUang));
			income.push(berasLine('Zakat Fitrah Beras', t.income.fitrahBeras));
			if (includePengeluaran) {
				outcome.push(moneyLine('Pengeluaran Zakat Fitrah Uang', t.outcome.fitrahUang));
				outcome.push(berasLine('Pengeluaran Zakat Fitrah Beras', t.outcome.fitrahBeras));
			}
			net.push(moneyLine('Saldo Net Zakat Fitrah Uang', t.income.fitrahUang - t.outcome.fitrahUang));
			net.push(berasLine('Saldo Net Zakat Fitrah Beras', t.income.fitrahBeras - t.outcome.fitrahBeras));
		}

		if (includeMaal) {
			income.push(moneyLine('Zakat Maal Uang', t.income.maalUang));
			if (includePengeluaran) {
				outcome.push(moneyLine('Pengeluaran Zakat Maal Uang', t.outcome.maalUang));
			}
			net.push(moneyLine('Saldo Net Zakat Maal', t.income.maalUang - t.outcome.maalUang));
		}

		if (includeFidyah) {
			income.push(moneyLine('Fidyah Uang', t.income.fidyahUang));
			income.push(berasLine('Fidyah Beras', t.income.fidyahBeras));
			if (includePengeluaran) {
				outcome.push(moneyLine('Pengeluaran Fidyah Uang', t.outcome.fidyahUang));
				outcome.push(berasLine('Pengeluaran Fidyah Beras', t.outcome.fidyahBeras));
			}
			net.push(moneyLine('Saldo Net Fidyah Uang', t.income.fidyahUang - t.outcome.fidyahUang));
			net.push(berasLine('Saldo Net Fidyah Beras', t.income.fidyahBeras - t.outcome.fidyahBeras));
		}

		if (includeInfaq) {
			income.push(moneyLine('Infaq Uang', t.income.infaqUang));
			if (includePengeluaran) {
				outcome.push(moneyLine('Pengeluaran Infaq Uang', t.outcome.infaqUang));
			}
			net.push(moneyLine('Saldo Net Infaq', t.income.infaqUang - t.outcome.infaqUang));
		}

		if (includeShodaqoh) {
			income.push(moneyLine('Shodaqoh Uang', t.income.shodaqohUang));
			if (includePengeluaran) {
				outcome.push(moneyLine('Pengeluaran Shodaqoh Uang', t.outcome.shodaqohUang));
			}
			net.push(moneyLine('Saldo Net Shodaqoh', t.income.shodaqohUang - t.outcome.shodaqohUang));
		}

		if (!includePengeluaran) {
			outcome.push('Pengeluaran tidak disertakan dalam laporan.');
		}

		const netUangTotal =
			(t.income.fitrahUang + t.income.maalUang + t.income.fidyahUang + t.income.infaqUang + t.income.shodaqohUang) -
			(t.outcome.fitrahUang + t.outcome.maalUang + t.outcome.fidyahUang + t.outcome.infaqUang + t.outcome.shodaqohUang);
		const netBerasTotal = (t.income.fitrahBeras + t.income.fidyahBeras) - (t.outcome.fitrahBeras + t.outcome.fidyahBeras);

		net.push(moneyLine('Saldo Net Total (Uang)', netUangTotal));
		net.push(berasLine('Saldo Net Total (Beras)', netBerasTotal));

		return { income, outcome, net };
	}

	function generatePdf() {
		if (!reportData) {
			alert('Silakan hitung laporan terlebih dahulu.');
			return;
		}

		const { income, outcome, net } = buildLines();
		const doc = new jsPDF();
		let y = 20;

		const addLine = (text: string, step = 7) => {
			if (y > 280) {
				doc.addPage();
				y = 20;
			}
			doc.text(text, 14, y);
			y += step;
		};

		addLine('Laporan Zakat', 10);
		addLine(`Tanggal: ${reportData.range.from} s/d ${reportData.range.to}`, 10);

		addLine('Pemasukan:', 8);
		income.forEach((line) => addLine(`- ${line}`));

		addLine('Pengeluaran:', 8);
		outcome.forEach((line) => addLine(`- ${line}`));

		addLine('Saldo Net:', 8);
		net.forEach((line) => addLine(`- ${line}`));

		doc.save(`laporan-zakat-${reportData.range.from}-sd-${reportData.range.to}.pdf`);
	}
</script>

<svelte:head>
	<title>Cetak Laporan - Zakat App</title>
</svelte:head>

<div class="space-y-6">
	<GlassCard title="Cetak Laporan">
		<p class="mb-4 text-sm text-white/50">
			Cetak laporan keuangan dalam format PDF dengan filter jenis laporan dan rentang tanggal.
		</p>

		<form method="POST" action="?/compute" use:enhance class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<p class="mb-2 block text-sm font-semibold text-white/80">Rentang Tanggal</p>
				<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
					<div>
						<label class="text-xs text-white/60" for="date_from">Dari</label>
						<input
							id="date_from"
							name="date_from"
							type="date"
							required
							class="mt-1 w-full rounded-md border border-white/15 bg-black/20 px-3 py-2 text-sm text-white"
						/>
					</div>
					<div>
						<label class="text-xs text-white/60" for="date_to">Sampai</label>
						<input
							id="date_to"
							name="date_to"
							type="date"
							required
							class="mt-1 w-full rounded-md border border-white/15 bg-black/20 px-3 py-2 text-sm text-white"
						/>
					</div>
				</div>
			</div>

			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<p class="mb-2 block text-sm font-semibold text-white/80">Pilih Laporan</p>
				<div class="grid grid-cols-1 gap-2 text-sm text-white/70 sm:grid-cols-2">
					<label class="flex items-center gap-2">
						<input type="checkbox" bind:checked={includeFitrah} class="rounded border-white/30" />
						Zakat Fitrah
					</label>
					<label class="flex items-center gap-2">
						<input type="checkbox" bind:checked={includeMaal} class="rounded border-white/30" />
						Zakat Maal
					</label>
					<label class="flex items-center gap-2">
						<input type="checkbox" bind:checked={includeFidyah} class="rounded border-white/30" />
						Fidyah
					</label>
					<label class="flex items-center gap-2">
						<input type="checkbox" bind:checked={includeInfaq} class="rounded border-white/30" />
						Infaq
					</label>
					<label class="flex items-center gap-2">
						<input type="checkbox" bind:checked={includeShodaqoh} class="rounded border-white/30" />
						Shodaqoh
					</label>
					<label class="flex items-center gap-2">
						<input type="checkbox" bind:checked={includePengeluaran} class="rounded border-white/30" />
						Pengeluaran
					</label>
				</div>
			</div>

			<div class="flex gap-3 md:col-span-2">
				<GlassButton type="submit">Preview Laporan</GlassButton>
				<GlassButton type="button" variant="outline" onclick={generatePdf}>
					Export PDF
				</GlassButton>
			</div>
		</form>
	</GlassCard>

	<GlassCard title="Preview Laporan">
		{#if !reportData}
			<p class="text-sm text-white/50">Belum ada data. Pilih rentang tanggal dan klik Hitung Laporan.</p>
		{:else}
			<div class="space-y-4 text-sm text-white/70">
				<div>
					<p class="font-semibold text-white/80">Tanggal: {reportData.range.from} s/d {reportData.range.to}</p>
				</div>
				<div>
					<p class="font-semibold text-white/80">Pemasukan</p>
					<ul class="mt-2 list-disc space-y-1 pl-5">
						{#each buildLines().income as line}
							<li>{line}</li>
						{/each}
					</ul>
				</div>
				<div>
					<p class="font-semibold text-white/80">Pengeluaran</p>
					<ul class="mt-2 list-disc space-y-1 pl-5">
						{#each buildLines().outcome as line}
							<li>{line}</li>
						{/each}
					</ul>
				</div>
				<div>
					<p class="font-semibold text-white/80">Saldo Net</p>
					<ul class="mt-2 list-disc space-y-1 pl-5">
						{#each buildLines().net as line}
							<li>{line}</li>
						{/each}
					</ul>
				</div>
			</div>
		{/if}
	</GlassCard>
</div>
