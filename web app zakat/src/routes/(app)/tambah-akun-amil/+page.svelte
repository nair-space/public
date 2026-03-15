<script lang="ts">
	import { enhance } from '$app/forms';
	import type { ActionData, PageData } from './$types';
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import GlassInput from '$lib/components/ui/GlassInput.svelte';
	import GlassButton from '$lib/components/ui/GlassButton.svelte';

	let { data, form }: { data: PageData; form: ActionData } = $props();
	let showSuccess = $state(false);
	let showResetSuccess = $state(false);
	let showDeleteConfirm = $state(false);
	let deleteUserId = $state('');
	let deleteUserName = $state('');

	let namaAmil = $state('');
	let idAmil = $state('');
	let password = $state('');
	let idError = $state('');
	let checkTimeout: ReturnType<typeof setTimeout> | null = null;

	$effect(() => {
		if (form?.success) {
			showSuccess = true;
			setTimeout(() => showSuccess = false, 3000);
			if (!form?.error) {
				resetForm();
			}
		}
	});

	$effect(() => {
		if (form?.message && form?.success) {
			showResetSuccess = true;
			setTimeout(() => showResetSuccess = false, 3000);
		}
	});

	function resetForm() {
		namaAmil = '';
		idAmil = '';
		password = '';
		idError = '';
	}

	function confirmDelete(userId: string, userName: string) {
		deleteUserId = userId;
		deleteUserName = userName;
		showDeleteConfirm = true;
	}

	function cancelDelete() {
		showDeleteConfirm = false;
		deleteUserId = '';
		deleteUserName = '';
	}

	async function checkIdDuplicate(value: string) {
		if (!value || value.trim() === '') {
			idError = '';
			return;
		}

		const formData = new FormData();
		formData.append('id', value);

		try {
			const response = await fetch('?/checkId', {
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
				idError = 'id ini sudah dipakai';
			} else {
				idError = '';
			}
		} catch (e) {
			console.error('Failed to check id:', e);
		}
	}

	function onIdInput(e: Event) {
		const value = (e.target as HTMLInputElement).value;
		idAmil = value;

		if (checkTimeout) {
			clearTimeout(checkTimeout);
		}

		checkTimeout = setTimeout(() => {
			checkIdDuplicate(value);
		}, 300);
	}
</script>

<svelte:head>
	<title>Tambah Akun Amil — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	{#if showSuccess}
		<div class="rounded-lg border border-emerald-500/30 bg-emerald-500/20 px-4 py-3 text-emerald-100">
			✅ Akun amil berhasil dibuat!
		</div>
	{/if}

	{#if showResetSuccess}
		<div class="rounded-lg border border-blue-500/30 bg-blue-500/20 px-4 py-3 text-blue-100">
			🔐 {form?.message}
		</div>
	{/if}

	{#if form?.error}
		<div class="rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-3 text-red-100">
			❌ {form.error}
		</div>
	{/if}

	<GlassCard title="Tambah Akun Amil">
		<form method="POST" action="?/create" use:enhance class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<GlassInput
				label="Nama Amil"
				name="nama_amil"
				placeholder="Masukkan nama amil"
				required
				bind:value={namaAmil}
			/>
			<GlassInput
				label="ID Login"
				name="id_amil"
				placeholder="Masukkan ID untuk login"
				required
				value={idAmil}
				error={idError}
				oninput={onIdInput}
			/>
			<GlassInput
				label="Password"
				name="password"
				type="password"
				placeholder="Masukkan password"
				required
				bind:value={password}
			/>
			<div></div>
			<div class="flex items-end gap-3 md:col-span-2">
				<GlassButton type="submit">💾 Simpan</GlassButton>
				<GlassButton type="button" variant="outline" onclick={resetForm}>🔄 Reset</GlassButton>
			</div>
		</form>
	</GlassCard>

	<!-- List of Amil Accounts -->
	<GlassCard title="Daftar Akun Amil">
		{#if data.amilList && data.amilList.length > 0}
			<div class="space-y-3">
				{#each data.amilList as amil (amil.id)}
					<div class="flex items-center justify-between rounded-lg border border-white/10 bg-white/5 p-3">
						<div class="flex flex-col">
							<span class="font-medium text-white">{amil.name}</span>
							<span class="text-sm text-white/60">ID: {amil.id}</span>
						</div>
						<div class="flex items-center gap-2">
							<form method="POST" action="?/resetPassword" use:enhance class="flex items-center gap-2">
								<input type="hidden" name="user_id" value={amil.id} />
								<GlassInput
									name="new_password"
									type="password"
									placeholder="Password baru"
									required
									size="sm"
								/>
								<GlassButton type="submit" variant="outline" size="sm">🔐 Ganti</GlassButton>
							</form>
							<GlassButton type="button" variant="danger" size="sm" onclick={() => confirmDelete(amil.id, amil.name)}>🗑️ Hapus</GlassButton>
						</div>
					</div>
				{/each}
			</div>
		{:else}
			<p class="text-white/50 text-center py-4">Belum ada akun amil</p>
		{/if}
	</GlassCard>
</div>

<!-- Delete Confirmation Modal -->
{#if showDeleteConfirm}
	<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
		<div class="rounded-xl border border-white/20 bg-slate-900/90 p-6 shadow-xl">
			<h3 class="mb-4 text-lg font-semibold text-white">yakin untuk hapus akun?</h3>
			<p class="mb-6 text-white/70">Akun <span class="font-medium text-white">{deleteUserName}</span> akan dihapus permanen.</p>
			<div class="flex justify-end gap-3">
				<GlassButton type="button" variant="outline" onclick={cancelDelete}>Tidak</GlassButton>
				<form method="POST" action="?/delete" use:enhance>
					<input type="hidden" name="user_id" value={deleteUserId} />
					<GlassButton type="submit" variant="danger">Ya</GlassButton>
				</form>
			</div>
		</div>
	</div>
{/if}
