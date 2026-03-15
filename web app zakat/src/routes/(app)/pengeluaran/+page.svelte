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
	let namaPengeluaran = $state('');
	let fitrahUangOut = $state('');
	let fitrahBerasOut = $state('');
	let maalUangOut = $state('');
	let fidyahUangOut = $state('');
	let fidyahBerasOut = $state('');
	let infaqUangOut = $state('');
	let shodaqohUangOut = $state('');
	let idNotaError = $state('');
	let checkTimeout: ReturnType<typeof setTimeout> | null = null;
	const entryError = $derived.by(() => {
		const values = [
			Number(fitrahUangOut || 0),
			Number(fitrahBerasOut || 0),
			Number(maalUangOut || 0),
			Number(fidyahUangOut || 0),
			Number(fidyahBerasOut || 0),
			Number(infaqUangOut || 0),
			Number(shodaqohUangOut || 0)
		];
		const count = values.filter((value) => value > 0).length;
		if (count > 1) return 'pastikan hanya input satu data saja';
		if (form?.error === 'pastikan hanya input satu data saja') return form.error;
		return '';
	});
	const hasEntryError = $derived.by(() => entryError.length > 0);

	// Calculated totals - stored in state and updated via effect
	let jumlahUangTotal = $state(0);
	let jumlahBerasTotal = $state(0);
	const jumlahPengeluaranUangDb = $derived.by(() =>
		data.pengeluaranList.reduce((total, record) => {
			return total
				+ Number(record.zakatFitrahUangOutcome || 0)
				+ Number(record.zakatMaalUangOutcome || 0)
				+ Number(record.fidyahUangOutcome || 0)
				+ Number(record.infaqUangOutcome || 0)
				+ Number(record.shodaqohUangOutcome || 0);
		}, 0)
	);
	const jumlahPengeluaranBerasDb = $derived.by(() =>
		data.pengeluaranList.reduce((total, record) => {
			return total
				+ Number(record.zakatFitrahBerasOutcome || 0)
				+ Number(record.fidyahBerasOutcome || 0);
		}, 0)
	);

	// Update calculated totals whenever inputs change
	$effect(() => {
		const fitrah = Number(fitrahUangOut || 0);
		const maal = Number(maalUangOut || 0);
		const fidyah = Number(fidyahUangOut || 0);
		const infaq = Number(infaqUangOut || 0);
		const shodaqoh = Number(shodaqohUangOut || 0);
		jumlahUangTotal = fitrah + maal + fidyah + infaq + shodaqoh;
		console.log('Uang calc:', { fitrah, maal, fidyah, infaq, shodaqoh, total: jumlahUangTotal });
	});

	$effect(() => {
		const fitrahBeras = Number(fitrahBerasOut || 0);
		const fidyahBeras = Number(fidyahBerasOut || 0);
		jumlahBerasTotal = fitrahBeras + fidyahBeras;
		console.log('Beras calc:', { fitrahBeras, fidyahBeras, total: jumlahBerasTotal });
	});

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
		namaPengeluaran = '';
		fitrahUangOut = '';
		fitrahBerasOut = '';
		maalUangOut = '';
		fidyahUangOut = '';
		fidyahBerasOut = '';
		infaqUangOut = '';
		shodaqohUangOut = '';
	}

	function editRecord(record: typeof data.pengeluaranList[0]) {
		editingId = record.id;
		idNota = record.idNotaInput?.toString() || '';
		namaPengeluaran = record.namaPengeluaran;
		// Handle bigint values by converting to Number first, then String
		fitrahUangOut = record.zakatFitrahUangOutcome ? String(Number(record.zakatFitrahUangOutcome)) : '';
		fitrahBerasOut = record.zakatFitrahBerasOutcome ? String(Number(record.zakatFitrahBerasOutcome)) : '';
		maalUangOut = record.zakatMaalUangOutcome ? String(Number(record.zakatMaalUangOutcome)) : '';
		fidyahUangOut = record.fidyahUangOutcome ? String(Number(record.fidyahUangOutcome)) : '';
		fidyahBerasOut = record.fidyahBerasOutcome ? String(Number(record.fidyahBerasOutcome)) : '';
		infaqUangOut = record.infaqUangOutcome ? String(Number(record.infaqUangOutcome)) : '';
		shodaqohUangOut = record.shodaqohUangOutcome ? String(Number(record.shodaqohUangOutcome)) : '';
		window.scrollTo({ top: 0, behavior: 'smooth' });
	}

	async function deleteRecord(record: typeof data.pengeluaranList[0]) {
		const shouldDelete = confirm('apakah anda yakin menghapus data?');
		if (!shouldDelete) {
			window.location.href = '/pengeluaran';
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
				alert('Gagal menghapus data pengeluaran');
			}
		} catch (e) {
			console.error('Failed to delete pengeluaran:', e);
			alert('Gagal menghapus data pengeluaran');
		}
	}

	function blockInvalidSubmit(event: SubmitEvent) {
		if (hasEntryError) {
			event.preventDefault();
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
			const exists = parsedData[1];

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

	// Input handlers for number fields
	function setFitrahUang(e: Event) { 
		fitrahUangOut = (e.target as HTMLInputElement).value; 
		console.log('setFitrahUang:', fitrahUangOut);
	}
	function setFitrahBeras(e: Event) { fitrahBerasOut = (e.target as HTMLInputElement).value; }
	function setMaalUang(e: Event) { maalUangOut = (e.target as HTMLInputElement).value; }
	function setFidyahUang(e: Event) { fidyahUangOut = (e.target as HTMLInputElement).value; }
	function setFidyahBeras(e: Event) { fidyahBerasOut = (e.target as HTMLInputElement).value; }
	function setInfaqUang(e: Event) { infaqUangOut = (e.target as HTMLInputElement).value; }
	function setShodaqohUang(e: Event) { shodaqohUangOut = (e.target as HTMLInputElement).value; }
	function setNamaPengeluaran(e: Event) { namaPengeluaran = (e.target as HTMLInputElement).value; }
</script>

<svelte:head>
	<title>Pengeluaran — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	{#if showSuccess}
		<div class="rounded-lg border border-emerald-500/30 bg-emerald-500/20 px-4 py-3 text-emerald-100">
			✅ Data pengeluaran berhasil {editingId ? 'diupdate' : 'disimpan'}!
		</div>
	{/if}

	{#if form?.error}
		<div class="rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-3 text-red-100">
			❌ {form.error}
		</div>
	{/if}

	<GlassCard title={editingId ? 'Edit Pengeluaran' : 'Input Pengeluaran'}>
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
			<GlassInput label="Nama Pengeluaran" name="nama_pengeluaran" placeholder="Keterangan pengeluaran" required value={namaPengeluaran} oninput={setNamaPengeluaran} />
			<GlassInput label="Zakat Fitrah Uang (Outcome)" name="zakat_fitrah_uang_outcome" type="number" placeholder="0" value={fitrahUangOut} oninput={setFitrahUang} />
			<GlassInput label="Zakat Fitrah Beras (Outcome / kg)" name="zakat_fitrah_beras_outcome" placeholder="0.00" value={fitrahBerasOut} oninput={setFitrahBeras} />
			<GlassInput label="Zakat Maal Uang (Outcome)" name="zakat_maal_uang_outcome" type="number" placeholder="0" value={maalUangOut} oninput={setMaalUang} />
			<GlassInput label="Fidyah Uang (Outcome)" name="fidyah_uang_outcome" type="number" placeholder="0" value={fidyahUangOut} oninput={setFidyahUang} />
			<GlassInput label="Fidyah Beras (Outcome / kg)" name="fidyah_beras_outcome" placeholder="0.00" value={fidyahBerasOut} oninput={setFidyahBeras} />
			<GlassInput label="Infaq Uang (Outcome)" name="infaq_uang_outcome" type="number" placeholder="0" value={infaqUangOut} oninput={setInfaqUang} />
			<GlassInput label="Shodaqoh Uang (Outcome)" name="shodaqoh_uang_outcome" type="number" placeholder="0" value={shodaqohUangOut} oninput={setShodaqohUang} />
			{#if entryError}
				<p class="md:col-span-2 text-xs text-red-300">{entryError}</p>
			{/if}

			<div class="flex items-end gap-3 md:col-span-2">
				<GlassButton
					type="submit"
					variant={hasEntryError ? 'danger' : 'primary'}
					disabled={hasEntryError}
				>
					{editingId ? '💾 Update' : '💾 Simpan Pengeluaran'}
				</GlassButton>
				<GlassButton type="button" variant="outline" onclick={resetForm}>
					{editingId ? '❌ Batal' : '🔄 Reset'}
				</GlassButton>
			</div>
		</form>
	</GlassCard>

	<GlassCard title="Ringkasan Pengeluaran">
		<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<div class="flex flex-col gap-1.5">
				<p class="text-sm font-medium text-white/70">Jumlah Pengeluaran (Uang / Rp)</p>
				<div class="glass-input px-4 py-2 text-white/80">
					{formatRupiah(jumlahPengeluaranUangDb)}
				</div>
				<p class="text-xs text-white/50">Akumulasi seluruh outcome uang di tabel pengeluaran</p>
			</div>
			<div class="flex flex-col gap-1.5">
				<p class="text-sm font-medium text-white/70">Jumlah Pengeluaran Beras (kg)</p>
				<div class="glass-input px-4 py-2 text-white/80">
					{jumlahPengeluaranBerasDb.toFixed(2)} kg
				</div>
				<p class="text-xs text-white/50">Akumulasi seluruh outcome beras di tabel pengeluaran</p>
			</div>
		</div>
	</GlassCard>

	<GlassCard title="Riwayat Pengeluaran">
		{#if data.pengeluaranList.length === 0}
			<div class="flex items-center justify-center py-8 text-white/30">
				<p class="text-sm">Belum ada data pengeluaran.</p>
			</div>
		{:else}
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead>
						<tr class="border-b border-white/10 text-left text-white/50">
							<th class="px-3 py-3 font-medium">ID Nota</th>
							<th class="px-3 py-3 font-medium">Nama Pengeluaran</th>
							<th class="px-3 py-3 text-right font-medium">Jumlah</th>
							<th class="px-3 py-3 font-medium">Tanggal</th>
							<th class="px-3 py-3 text-center font-medium">Aksi</th>
						</tr>
					</thead>
					<tbody>
						{#each data.pengeluaranList as record (record.id)}
							<tr class="border-b border-white/5 transition-colors hover:bg-white/5">
								<td class="px-3 py-3 text-white/80">#{record.idNotaInput}</td>
								<td class="px-3 py-3 text-white/80">{record.namaPengeluaran}</td>
								<td class="px-3 py-3 text-right text-rose-400">
								{#if true}
									{@const totalUang = Number(record.zakatFitrahUangOutcome || 0) + Number(record.zakatMaalUangOutcome || 0) + Number(record.fidyahUangOutcome || 0) + Number(record.infaqUangOutcome || 0) + Number(record.shodaqohUangOutcome || 0)}
									{@const totalBeras = Number(record.zakatFitrahBerasOutcome || 0) + Number(record.fidyahBerasOutcome || 0)}
									{#if totalUang > 0}
										<div>{formatRupiah(totalUang)}</div>
									{/if}
									{#if totalBeras > 0}
										<div>{totalBeras.toFixed(2)} kg</div>
									{/if}
									{#if totalUang === 0 && totalBeras === 0}
										<div>-</div>
									{/if}
								{/if}
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


