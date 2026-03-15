<script lang="ts">
	import type { PageData, ActionData } from './$types';
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import GlassInput from '$lib/components/ui/GlassInput.svelte';
	import GlassButton from '$lib/components/ui/GlassButton.svelte';
	import { formatRupiah } from '$lib/utils/currency';
	import { enhance } from '$app/forms';

	let { data, form }: { data: PageData; form: ActionData } = $props();
	let showSuccess = $state(false);
	let showSettingsSuccess = $state(false);
	let editingId = $state<number | null>(null);

	let namaAmil = $state('');
	let absenAmil = $state('');

	// Settings form values
	let settingsFeeDasar = $state('');
	let settingsJatah = $state('');
	let settingsInitialized = $state(false);

	$effect(() => {
		if (settingsInitialized) return;
		settingsFeeDasar = data.settings?.feeDasar?.toString() || '';
		settingsJatah = data.settings?.jatahAmil?.toString() || '';
		settingsInitialized = true;
	});

	// Global fee dasar from settings
	const globalFeeDasar = $derived(() => data.settings?.feeDasar || 0);
	const globalJatah = $derived(() => data.settings?.jatahAmil || 0);

	// Reactive fee calculation using global fee_dasar
	const calculatedFeeTotal = $derived(() => {
		const absen = parseInt(absenAmil) || 0;
		const dasar = globalFeeDasar();
		return absen * dasar;
	});

	$effect(() => {
		if (form?.success) {
			const isSettingsUpdated = 'settingsUpdated' in form && form.settingsUpdated === true;
			if (isSettingsUpdated) {
				showSettingsSuccess = true;
				setTimeout(() => showSettingsSuccess = false, 3000);
			} else {
				showSuccess = true;
				setTimeout(() => showSuccess = false, 3000);
			}
			if (!form?.error) {
				if (isSettingsUpdated) {
					resetSettingsForm();
				} else {
					resetForm();
				}
			}
		}
	});

	function resetForm() {
		editingId = null;
		namaAmil = '';
		absenAmil = '';
	}

	function resetSettingsForm() {
		settingsFeeDasar = data.settings?.feeDasar?.toString() || '';
		settingsJatah = data.settings?.jatahAmil?.toString() || '';
	}

	function editRecord(record: typeof data.amilList[0]) {
		editingId = record.id;
		namaAmil = record.namaAmil;
		absenAmil = record.absenAmil.toString();
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}

	async function deleteRecord(record: typeof data.amilList[0]) {
		if (!confirm(`Hapus data amil "${record.namaAmil}"?`)) {
			return;
		}

		const formData = new FormData();
		formData.append('id', record.id.toString());

		try {
			const response = await fetch('?/delete', {
				method: 'POST',
				body: formData
			});
			const result = await response.json();

			if (result.type === 'success') {
				window.location.reload();
			} else {
				alert('Gagal menghapus data amil');
			}
		} catch (e) {
			console.error('Failed to delete amil:', e);
			alert('Gagal menghapus data amil');
		}
	}

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

	// Calculate total fee of all amil
	const totalFeeAllAmil = $derived(() => {
		return data.amilList.reduce((sum, amil) => sum + (amil.feeTotalAmil || 0), 0);
	});

	// Check if total fee exceeds jatah
	const exceedsJatah = $derived(() => {
		const jatah = globalJatah();
		if (jatah === 0) return false;
		const currentTotal = totalFeeAllAmil();
		const thisAmilFee = calculatedFeeTotal();
		const oldFee = editingId ? (data.amilList.find(a => a.id === editingId)?.feeTotalAmil || 0) : 0;
		return (currentTotal - oldFee + thisAmilFee) > jatah;
	});
</script>

<svelte:head>
	<title>Amil & Volunteer — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	{#if showSuccess}
		<div class="rounded-lg border border-emerald-500/30 bg-emerald-500/20 px-4 py-3 text-emerald-100">
			✅ Data amil berhasil {editingId ? 'diupdate' : 'disimpan'}!
		</div>
	{/if}

	{#if showSettingsSuccess}
		<div class="rounded-lg border border-emerald-500/30 bg-emerald-500/20 px-4 py-3 text-emerald-100">
			✅ Pengaturan berhasil diupdate!
		</div>
	{/if}

	{#if form?.error}
		<div class="rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-3 text-red-100">
			❌ {form.error}
		</div>
	{/if}

	{#if exceedsJatah()}
		<div class="rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-3 text-red-100">
			⚠️ fee amil melewati jatah amil
		</div>
	{/if}

	<!-- Settings Card -->
	<GlassCard title="Pengaturan Global">
		<form method="POST" action="?/updateSettings" use:enhance class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<GlassInput label="Fee Dasar (Rp)" name="fee_dasar" type="number" placeholder="30000" bind:value={settingsFeeDasar} />
			<GlassInput label="Jatah Amil (Rp)" name="jatah_amil" type="number" placeholder="2000000" bind:value={settingsJatah} />

			<div class="flex items-end gap-3 md:col-span-2">
				<GlassButton type="submit">💾 Simpan Pengaturan</GlassButton>
				<GlassButton type="button" variant="outline" onclick={resetSettingsForm}>🔄 Reset</GlassButton>
			</div>
		</form>
	</GlassCard>

	<GlassCard title={editingId ? 'Edit Amil / Volunteer' : 'Tambah Amil / Volunteer'}>
		<form method="POST" action={editingId ? '?/update' : '?/save'} use:enhance class="grid grid-cols-1 gap-4 md:grid-cols-2">
			{#if editingId}
				<input type="hidden" name="id" value={editingId} />
			{/if}
			<GlassInput label="Nama Amil" name="nama_amil" placeholder="Masukkan nama" required bind:value={namaAmil} />
			<GlassInput label="Absen Amil (1-5)" name="absen_amil" type="number" placeholder="1" bind:value={absenAmil} />

			<!-- Display Global Fee Dasar -->
			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<p class="text-sm text-white/60">Fee Dasar Global</p>
				<p class="mt-1 text-xl font-bold text-white/80">
					{formatRupiah(globalFeeDasar())}
				</p>
			</div>

			<!-- Display Global Jatah -->
			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<p class="text-sm text-white/60">Jatah Amil Global</p>
				<p class="mt-1 text-xl font-bold text-white/80">
					{formatRupiah(globalJatah())}
				</p>
			</div>

			<!-- Calculated Fee Display -->
			<div class="rounded-xl border border-primary-500/20 bg-primary-500/10 p-4 md:col-span-2">
				<p class="text-sm text-white/60">Fee Total (Absen × Fee Dasar Global)</p>
				<p class="mt-1 text-2xl font-bold text-primary-300">
					{formatRupiah(calculatedFeeTotal())}
				</p>
				<p class="mt-1 text-xs text-white/40">
					= {absenAmil || '0'} × {formatRupiah(globalFeeDasar())}
				</p>
			</div>

			<!-- Total Fee All Amil Display -->
			{#if data.amilList.length > 0}
				<div class="rounded-xl border border-white/10 bg-white/5 p-4 md:col-span-2">
					<p class="text-sm text-white/60">Total Fee Semua Amil</p>
					<p class="mt-1 text-xl font-bold text-white/80">
						{formatRupiah(totalFeeAllAmil())}
					</p>
					<p class="mt-1 text-xs text-white/40">
						Jatah: {formatRupiah(globalJatah())} | Sisa: {formatRupiah(Math.max(0, globalJatah() - totalFeeAllAmil() + (editingId ? (data.amilList.find(a => a.id === editingId)?.feeTotalAmil || 0) : 0)))}
					</p>
				</div>
			{/if}

			<div class="flex items-end gap-3 md:col-span-2">
				<GlassButton type="submit" disabled={exceedsJatah()}>
					{editingId ? '💾 Update' : '💾 Simpan Amil'}
				</GlassButton>
				<GlassButton type="button" variant="outline" onclick={resetForm}>
					{editingId ? '❌ Batal' : '🔄 Reset'}
				</GlassButton>
			</div>
		</form>
	</GlassCard>

	<GlassCard title="Daftar Amil & Volunteer">
		{#if data.amilList.length === 0}
			<div class="flex items-center justify-center py-8 text-white/30">
				<p class="text-sm">Belum ada data amil.</p>
			</div>
		{:else}
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead>
						<tr class="border-b border-white/10 text-left text-white/50">
							<th class="px-3 py-3 font-medium">Nama</th>
							<th class="px-3 py-3 text-center font-medium">Absen</th>
							<th class="px-3 py-3 text-right font-medium">Fee Dasar</th>
							<th class="px-3 py-3 text-right font-medium">Fee Total</th>
							<th class="px-3 py-3 text-right font-medium">Jatah</th>
							<th class="px-3 py-3 font-medium">Tanggal</th>
							<th class="px-3 py-3 text-center font-medium">Aksi</th>
						</tr>
					</thead>
					<tbody>
						{#each data.amilList as record (record.id)}
							<tr class="border-b border-white/5 transition-colors hover:bg-white/5">
								<td class="px-3 py-3 text-white/80">{record.namaAmil}</td>
								<td class="px-3 py-3 text-center text-white/80">{record.absenAmil}</td>
								<td class="px-3 py-3 text-right text-white/60">{formatRupiah(globalFeeDasar())}</td>
								<td class="px-3 py-3 text-right font-medium text-primary-300">{formatRupiah(record.feeTotalAmil)}</td>
								<td class="px-3 py-3 text-right text-white/60">{formatRupiah(globalJatah())}</td>
								<td class="px-3 py-3 text-xs text-white/60">{formatDate(record.createdAt)}</td>
								<td class="px-3 py-3 text-center">
									<div class="flex items-center justify-center gap-2">
										<GlassButton variant="outline" onclick={() => editRecord(record)}>
											✏️ Edit
										</GlassButton>
										<GlassButton variant="danger" size="sm" onclick={() => deleteRecord(record)}>
											🗑️ Delete
										</GlassButton>
									</div>
								</td>
							</tr>
						{/each}
					</tbody>
				</table>
			</div>
		{/if}
	</GlassCard>
</div>
