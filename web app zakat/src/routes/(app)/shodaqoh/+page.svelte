<script lang="ts">
	import { enhance } from '$app/forms';
	import type { ActionData, PageData } from './$types';
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import GlassInput from '$lib/components/ui/GlassInput.svelte';
	import GlassButton from '$lib/components/ui/GlassButton.svelte';
	import { formatRupiah } from '$lib/utils/currency';

	let { data, form }: { data: PageData; form: ActionData } = $props();
	let showSuccess = $state(false);
	let editingId = $state<number | null>(null);

	let idNota = $state('');
	let nama = $state('');
	let shodaqohUang = $state('');
	let idNotaError = $state('');
	let checkTimeout: ReturnType<typeof setTimeout> | null = null;
	const jumlahShodaqohUangDb = $derived.by(() =>
		data.shodaqohList.reduce((total, record) => {
			return total + Number(record.shodaqohUangIncome || 0);
		}, 0)
	);

	$effect(() => {
		if (form?.success) {
			showSuccess = true;
			setTimeout(() => showSuccess = false, 3000);
			if (!form?.error) {
				resetForm();
			}
		}
	});

	function resetForm() {
		editingId = null;
		idNota = '';
		idNotaError = '';
		nama = '';
		shodaqohUang = '';
	}

	function editRecord(record: typeof data.shodaqohList[0]) {
		editingId = record.id;
		idNota = record.idNotaInput?.toString() || '';
		nama = record.nama;
		shodaqohUang = record.shodaqohUangIncome?.toString() || '';
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}

	async function deleteRecord(record: typeof data.shodaqohList[0]) {
		const shouldDelete = confirm('apakah anda yakin menghapus data?');
		if (!shouldDelete) return;

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
				alert('Gagal menghapus data shodaqoh');
			}
		} catch (e) {
			console.error('Failed to delete shodaqoh:', e);
			alert('Gagal menghapus data shodaqoh');
		}
	}

	async function checkIdNotaDuplicate(value: string) {
		if (!value || value.trim() === '') {
			idNotaError = '';
			return;
		}

		const formData = new FormData();
		formData.append('id_nota', value);

		try {
			const response = await fetch('?/checkIdNota', {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'sveltekit-action': 'true'
				},
				body: formData
			});
			const text = await response.text();
			const result = JSON.parse(text);
			const parsedData = JSON.parse(result.data);
			const exists = parsedData[1]; // This is directly true/false

			if (exists) {
				idNotaError = 'id nota ini sudah dipakai';
			} else {
				idNotaError = '';
			}
		} catch (e) {
			console.error('Failed to check id_nota:', e);
		}
	}

	function onIdNotaInput(e: Event) {
		const value = (e.target as HTMLInputElement).value;
		idNota = value;

		if (checkTimeout) {
			clearTimeout(checkTimeout);
		}

		checkTimeout = setTimeout(() => {
			checkIdNotaDuplicate(value);
		}, 300);
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
</script>

<svelte:head>
	<title>Shodaqoh — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	{#if showSuccess}
		<div class="rounded-lg border border-emerald-500/30 bg-emerald-500/20 px-4 py-3 text-emerald-100">
			✅ Data shodaqoh berhasil {editingId ? 'diupdate' : 'disimpan'}!
		</div>
	{/if}

	{#if form?.error}
		<div class="rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-3 text-red-100">
			❌ {form.error}
		</div>
	{/if}

	<GlassCard title={editingId ? 'Edit Shodaqoh' : 'Input Shodaqoh'}>
		<form method="POST" action={editingId ? '?/update' : '?/save'} use:enhance class="grid grid-cols-1 gap-4 md:grid-cols-2">
			{#if editingId}
				<input type="hidden" name="id" value={editingId} />
			{/if}
			<GlassInput label="ID Nota Input" name="id_nota" placeholder="Auto-generated" value={idNota} error={idNotaError} oninput={onIdNotaInput} />
			<GlassInput label="Nama" name="nama" placeholder="Masukkan nama" required bind:value={nama} />
			<GlassInput label="Shodaqoh (Uang / Rp)" name="shodaqoh_uang" type="number" placeholder="0" bind:value={shodaqohUang} />

			<div class="flex items-end gap-3 md:col-span-2">
				<GlassButton type="submit">{editingId ? '💾 Update' : '💾 Simpan'}</GlassButton>
				<GlassButton type="button" variant="outline" onclick={resetForm}>
					{editingId ? '❌ Batal' : '🔄 Reset'}
				</GlassButton>
			</div>
		</form>
	</GlassCard>

	<GlassCard title="Ringkasan Pemasukan">
		<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<div class="flex flex-col gap-1.5">
				<p class="text-sm font-medium text-white/70">Jumlah Pemasukan Shodaqoh (Uang / Rp)</p>
				<div class="glass-input px-4 py-2 text-white/80">
					{formatRupiah(jumlahShodaqohUangDb)}
				</div>
				<p class="text-xs text-white/50">Akumulasi seluruh shodaqoh uang</p>
			</div>
		</div>
	</GlassCard>

	<GlassCard title="Riwayat Shodaqoh">
		{#if data.shodaqohList.length === 0}
			<div class="flex items-center justify-center py-8 text-white/30">
				<p class="text-sm">Belum ada data. Silakan input shodaqoh di atas.</p>
			</div>
		{:else}
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead>
						<tr class="border-b border-white/10 text-left text-white/50">
							<th class="px-3 py-3 font-medium">ID Nota</th>
							<th class="px-3 py-3 font-medium">Nama</th>
							<th class="px-3 py-3 text-right font-medium">Shodaqoh Uang</th>
							<th class="px-3 py-3 font-medium">Tanggal</th>
							<th class="px-3 py-3 text-center font-medium">Aksi</th>
						</tr>
					</thead>
					<tbody>
						{#each data.shodaqohList as record (record.id)}
							<tr class="border-b border-white/5 transition-colors hover:bg-white/5">
								<td class="px-3 py-3 text-white/80">#{record.idNotaInput}</td>
								<td class="px-3 py-3 text-white/80">{record.nama}</td>
								<td class="px-3 py-3 text-right text-emerald-400">
									{record.shodaqohUangIncome ? formatRupiah(record.shodaqohUangIncome) : '-'}
								</td>
								<td class="px-3 py-3 text-xs text-white/60">{formatDate(record.createdAt)}</td>
								<td class="px-3 py-3 text-center">
									<div class="flex items-center justify-center gap-2">
										<GlassButton variant="outline" onclick={() => editRecord(record)}>
											Edit
										</GlassButton>
										<GlassButton variant="danger" size="sm" onclick={() => deleteRecord(record)}>
											Hapus
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


