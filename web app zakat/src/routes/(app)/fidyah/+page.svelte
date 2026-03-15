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
	let fidyahUang = $state('');
	let fidyahBeras = $state('');
	let idNotaError = $state('');
	let checkTimeout: ReturnType<typeof setTimeout> | null = null;
	const entryError = $derived.by(() => {
		const count = [Number(fidyahUang || 0), Number(fidyahBeras || 0)].filter((value) => value > 0).length;
		if (count > 1) return 'pastikan hanya input satu data saja';
		if (form?.error === 'pastikan hanya input satu data saja') return form.error;
		return '';
	});
	const hasEntryError = $derived.by(() => entryError.length > 0);
	const jumlahFidyahUangDb = $derived.by(() =>
		data.fidyahList.reduce((total, record) => {
			return total + Number(record.fidyahUangIncome || 0);
		}, 0)
	);
	const jumlahFidyahBerasDb = $derived.by(() =>
		data.fidyahList.reduce((total, record) => {
			return total + Number(record.fidyahBerasIncome || 0);
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
		fidyahUang = '';
		fidyahBeras = '';
	}

	function editRecord(record: typeof data.fidyahList[0]) {
		editingId = record.id;
		idNota = record.idNotaInput?.toString() || '';
		nama = record.nama;
		fidyahUang = record.fidyahUangIncome?.toString() || '';
		fidyahBeras = record.fidyahBerasIncome?.toString() || '';
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}

	function blockInvalidSubmit(event: SubmitEvent) {
		if (hasEntryError) {
			event.preventDefault();
		}
	}

	async function deleteRecord(record: typeof data.fidyahList[0]) {
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
				alert('Gagal menghapus data fidyah');
			}
		} catch (e) {
			console.error('Failed to delete fidyah:', e);
			alert('Gagal menghapus data fidyah');
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
	<title>Fidyah — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	{#if showSuccess}
		<div class="rounded-lg border border-emerald-500/30 bg-emerald-500/20 px-4 py-3 text-emerald-100">
			✅ Data fidyah berhasil {editingId ? 'diupdate' : 'disimpan'}!
		</div>
	{/if}

	{#if form?.error}
		<div class="rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-3 text-red-100">
			❌ {form.error}
		</div>
	{/if}

	<GlassCard title={editingId ? 'Edit Fidyah' : 'Input Fidyah'}>
		<form
			method="POST"
			action={editingId ? '?/update' : '?/save'}
			use:enhance
			onsubmit={blockInvalidSubmit}
			class="grid grid-cols-1 gap-4 md:grid-cols-2"
		>
			{#if editingId}
				<input type="hidden" name="id" value={editingId} />
			{/if}
			<GlassInput label="ID Nota Input" name="id_nota" placeholder="Auto-generated" value={idNota} error={idNotaError} oninput={onIdNotaInput} />
			<GlassInput label="Nama" name="nama" placeholder="Masukkan nama" required bind:value={nama} />
			<GlassInput label="Fidyah (Uang / Rp)" name="fidyah_uang" type="number" placeholder="0" bind:value={fidyahUang} />
			<GlassInput label="Fidyah (Beras / kg)" name="fidyah_beras" placeholder="0.00" bind:value={fidyahBeras} />
			{#if entryError}
				<p class="md:col-span-2 text-xs text-red-300">{entryError}</p>
			{/if}

			<div class="flex items-end gap-3 md:col-span-2">
				<GlassButton
					type="submit"
					variant={hasEntryError ? 'danger' : 'primary'}
					disabled={hasEntryError}
				>
					{editingId ? '💾 Update' : '💾 Simpan'}
				</GlassButton>
				<GlassButton type="button" variant="outline" onclick={resetForm}>
					{editingId ? '❌ Batal' : '🔄 Reset'}
				</GlassButton>
			</div>
		</form>
	</GlassCard>

	<GlassCard title="Ringkasan Pemasukan">
		<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<div class="flex flex-col gap-1.5">
				<p class="text-sm font-medium text-white/70">Jumlah Pemasukan Fidyah (Uang / Rp)</p>
				<div class="glass-input px-4 py-2 text-white/80">
					{formatRupiah(jumlahFidyahUangDb)}
				</div>
				<p class="text-xs text-white/50">Akumulasi seluruh fidyah uang</p>
			</div>
			<div class="flex flex-col gap-1.5">
				<p class="text-sm font-medium text-white/70">Jumlah Pemasukan Fidyah (Beras / kg)</p>
				<div class="glass-input px-4 py-2 text-white/80">
					{jumlahFidyahBerasDb.toFixed(2)} kg
				</div>
				<p class="text-xs text-white/50">Akumulasi seluruh fidyah beras</p>
			</div>
		</div>
	</GlassCard>

	<GlassCard title="Riwayat Fidyah">
		{#if data.fidyahList.length === 0}
			<div class="flex items-center justify-center py-8 text-white/30">
				<p class="text-sm">Belum ada data. Silakan input fidyah di atas.</p>
			</div>
		{:else}
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead>
						<tr class="border-b border-white/10 text-left text-white/50">
							<th class="px-3 py-3 font-medium">ID Nota</th>
							<th class="px-3 py-3 font-medium">Nama</th>
							<th class="px-3 py-3 text-right font-medium">Fidyah Uang</th>
							<th class="px-3 py-3 text-right font-medium">Fidyah Beras</th>
							<th class="px-3 py-3 font-medium">Tanggal</th>
							<th class="px-3 py-3 text-center font-medium">Aksi</th>
						</tr>
					</thead>
					<tbody>
						{#each data.fidyahList as record (record.id)}
							<tr class="border-b border-white/5 transition-colors hover:bg-white/5">
								<td class="px-3 py-3 text-white/80">#{record.idNotaInput}</td>
								<td class="px-3 py-3 text-white/80">{record.nama}</td>
								<td class="px-3 py-3 text-right text-emerald-400">
									{record.fidyahUangIncome ? formatRupiah(record.fidyahUangIncome) : '-'}
								</td>
								<td class="px-3 py-3 text-right text-amber-400">
									{record.fidyahBerasIncome ? `${record.fidyahBerasIncome} kg` : '-'}
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


