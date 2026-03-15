<script lang="ts">
	import { enhance } from '$app/forms';
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import GlassButton from '$lib/components/ui/GlassButton.svelte';
	import type { ActionData } from './$types';

	let { form }: { form: ActionData } = $props();
	let exportJobId: number | null = $state(null);
	let exportStatus: 'idle' | 'pending' | 'processing' | 'completed' | 'failed' = $state('idle');
	let exportMessage: string | null = $state(null);
	let exportDownload: { filename: string; mimeType: string; contentBase64: string } | null = $state(null);
	let pollTimer: ReturnType<typeof setTimeout> | null = null;

	function confirmImport(event: SubmitEvent) {
		const ok = confirm('Import akan me-restore database dan mengganti data saat ini. Lanjutkan?');
		if (!ok) {
			event.preventDefault();
		}
	}

	function downloadFromBase64(filename: string, mimeType: string, contentBase64: string) {
		const bytes = Uint8Array.from(atob(contentBase64), (char) => char.charCodeAt(0));
		const blob = new Blob([bytes], { type: mimeType });
		const url = URL.createObjectURL(blob);
		const link = document.createElement('a');
		link.href = url;
		link.download = filename;
		document.body.appendChild(link);
		link.click();
		link.remove();
		URL.revokeObjectURL(url);
	}

	function schedulePoll() {
		if (pollTimer) clearTimeout(pollTimer);
		pollTimer = setTimeout(() => {
			void pollExportStatus();
		}, 2000);
	}

	async function pollExportStatus() {
		if (!exportJobId) return;
		const formData = new FormData();
		formData.set('job_id', String(exportJobId));
		const response = await fetch('?/exportStatus', { method: 'POST', body: formData });
		const payload = (await response.json()) as {
			success?: boolean;
			status?: 'pending' | 'processing' | 'completed' | 'failed';
			message?: string | null;
			download?: { filename: string; mimeType: string; contentBase64: string } | null;
		};

		if (!payload?.success) {
			exportStatus = 'failed';
			exportMessage = payload?.message || 'Gagal memuat status export.';
			return;
		}

		exportStatus = payload.status ?? 'pending';
		exportMessage = payload.message || null;

		if (payload.download) {
			exportDownload = payload.download;
			downloadFromBase64(
				payload.download.filename,
				payload.download.mimeType,
				payload.download.contentBase64
			);
			return;
		}

		if (exportStatus !== 'completed' && exportStatus !== 'failed') {
			schedulePoll();
		}
	}

	function handleExportEnhance() {
		return async ({ result, update }: { result: { type: string; data?: unknown }; update: () => Promise<void> }) => {
			await update();
			if (result.type !== 'success' || !result.data) return;

			const payload = result.data as {
				jobId?: number;
				status?: 'pending' | 'processing' | 'completed' | 'failed';
				message?: string;
			};

			if (payload.jobId) {
				exportJobId = payload.jobId;
				exportStatus = payload.status ?? 'pending';
				exportMessage = payload.message || null;
				exportDownload = null;
				schedulePoll();
			}
		};
	}
</script>

<svelte:head>
	<title>Import / Export - Zakat App</title>
</svelte:head>

<div class="space-y-6">
	{#if form?.success}
		<div class="rounded-lg border border-emerald-500/30 bg-emerald-500/20 px-4 py-3 text-emerald-100">
			{form.message || 'Proses backup/restore berhasil.'}
		</div>
	{/if}

	{#if form?.error}
		<div class="rounded-lg border border-red-500/30 bg-red-500/20 px-4 py-3 text-red-100">
			{form.error}
		</div>
	{/if}

	<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
		<GlassCard title="Import / Restore Backup">
			<p class="mb-4 text-sm text-white/50">
				Upload file backup untuk restore database. Data lama akan diganti data dari file.
			</p>

			<form
				method="POST"
				action="?/import"
				enctype="multipart/form-data"
				use:enhance
				onsubmit={confirmImport}
				class="space-y-4"
			>
				<div class="rounded-xl border border-white/15 bg-white/5 p-4">
					<label class="mb-2 block text-sm text-white/70" for="backup_file">Pilih File Backup</label>
					<input
						id="backup_file"
						type="file"
						name="backup_file"
						accept=".csv,.xlsx,.sql"
						required
						class="w-full rounded-md border border-white/15 bg-black/20 px-3 py-2 text-sm text-white file:mr-3 file:rounded file:border-0 file:bg-primary-500/20 file:px-3 file:py-1 file:text-white"
					/>
					<p class="mt-2 text-xs text-white/40">Format didukung: .csv, .xlsx, .sql</p>
				</div>
				<GlassButton class="w-full" type="submit" variant="outline">
					Restore Database
				</GlassButton>
			</form>
		</GlassCard>

		<GlassCard title="Export / Backup Data">
			<p class="mb-4 text-sm text-white/50">
				Download backup seluruh data aplikasi dalam format .csv, .xlsx, atau .sql.
			</p>

			<form method="POST" action="?/export" use:enhance={handleExportEnhance} class="space-y-4">
				<div class="rounded-xl border border-white/15 bg-white/5 p-4">
					<label class="mb-2 block text-sm text-white/70" for="backup_format">Format Backup</label>
					<select
						id="backup_format"
						name="format"
						class="w-full rounded-md border border-white/15 bg-black/20 px-3 py-2 text-sm text-white"
					>
						<option value="csv">CSV (.csv)</option>
						<option value="xlsx">Excel (.xlsx)</option>
						<option value="sql">SQL (.sql)</option>
					</select>
				</div>
				<GlassButton class="w-full" type="submit" variant="outline">
					Export Backup
				</GlassButton>
			</form>

			{#if exportStatus !== 'idle'}
				<div class="mt-4 rounded-lg border border-white/10 bg-black/20 px-3 py-2 text-xs text-white/70">
					Status export: {exportStatus}
					{#if exportMessage}
						<div class="mt-1 text-white/50">{exportMessage}</div>
					{/if}
				</div>
			{/if}

			{#if exportDownload}
				<div class="mt-3 text-xs text-emerald-300">
					File siap diunduh: {exportDownload.filename}
				</div>
			{/if}
		</GlassCard>
	</div>
</div>
