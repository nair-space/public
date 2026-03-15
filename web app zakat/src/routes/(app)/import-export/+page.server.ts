import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import {
	amil,
	amilSettings,
	exportJobs,
	fidyah,
	infaq,
	laporanDaily,
	laporanMonthly,
	pengeluaran,
	shodaqoh,
	zakatFitrah,
	zakatMaal
} from '$lib/server/db/schema';
import { and, desc, eq, sql } from 'drizzle-orm';
import * as XLSX from 'xlsx';

type BackupRow = {
	table: string;
	data_base64: string;
};

type ExportJobStatus = 'pending' | 'processing' | 'completed' | 'failed';

const tableConfigs = [
	{ name: 'zakat_fitrah', table: zakatFitrah, omitOnInsert: ['id'] },
	{ name: 'zakat_maal', table: zakatMaal, omitOnInsert: ['id'] },
	{ name: 'fidyah', table: fidyah, omitOnInsert: ['id'] },
	{ name: 'infaq', table: infaq, omitOnInsert: ['id'] },
	{ name: 'shodaqoh', table: shodaqoh, omitOnInsert: ['id'] },
	{ name: 'pengeluaran', table: pengeluaran, omitOnInsert: ['id'] },
	{ name: 'amil', table: amil, omitOnInsert: ['id'] },
	{ name: 'amil_settings', table: amilSettings, omitOnInsert: [] },
	{ name: 'laporan_daily', table: laporanDaily, omitOnInsert: [] },
	{ name: 'laporan_monthly', table: laporanMonthly, omitOnInsert: [] }
] as const;

type TableName = (typeof tableConfigs)[number]['name'];
type BackupDataMap = Record<TableName, Record<string, unknown>[]>;

function toBase64(input: string): string {
	return Buffer.from(input, 'utf-8').toString('base64');
}

function fromBase64(input: string): string {
	return Buffer.from(input, 'base64').toString('utf-8');
}

function serializeValue(value: unknown): unknown {
	if (value instanceof Date) return value.toISOString();
	return value;
}

function prepareRowForInsert(row: Record<string, unknown>, omitKeys: readonly string[]) {
	const nextRow: Record<string, unknown> = {};

	for (const [key, value] of Object.entries(row)) {
		if (omitKeys.includes(key)) continue;
		if (value === null || value === undefined) {
			nextRow[key] = value;
			continue;
		}

		if (key.endsWith('At') && typeof value === 'string') {
			nextRow[key] = new Date(value);
			continue;
		}

		nextRow[key] = value;
	}

	return nextRow;
}

function parseBackupRowsFromCsv(content: string): BackupRow[] {
	const lines = content
		.replace(/\r/g, '')
		.split('\n')
		.filter((line) => line.trim().length > 0);

	if (lines.length === 0) return [];
	if (lines[0] !== 'table,data_base64') {
		throw new Error('Format CSV tidak valid');
	}

	return lines.slice(1).map((line) => {
		const firstComma = line.indexOf(',');
		if (firstComma < 0) {
			throw new Error('Baris CSV tidak valid');
		}
		return {
			table: line.slice(0, firstComma).trim(),
			data_base64: line.slice(firstComma + 1).trim()
		};
	});
}

function parseBackupRowsFromXlsx(buffer: Buffer): BackupRow[] {
	const workbook = XLSX.read(buffer, { type: 'buffer' });
	const firstSheetName = workbook.SheetNames[0];
	if (!firstSheetName) return [];
	const sheet = workbook.Sheets[firstSheetName];
	const rows = XLSX.utils.sheet_to_json<{ table?: string; data_base64?: string }>(sheet, {
		defval: ''
	});

	return rows
		.filter((row: { table?: string; data_base64?: string }) => row.table && row.data_base64)
		.map((row: { table?: string; data_base64?: string }) => ({
			table: String(row.table),
			data_base64: String(row.data_base64)
		}));
}

function backupRowsToMap(rows: BackupRow[]): BackupDataMap {
	const map: BackupDataMap = {
		zakat_fitrah: [],
		zakat_maal: [],
		fidyah: [],
		infaq: [],
		shodaqoh: [],
		pengeluaran: [],
		amil: [],
		amil_settings: [],
		laporan_daily: [],
		laporan_monthly: []
	};

	for (const row of rows) {
		const tableName = row.table as TableName;
		if (!(tableName in map)) continue;
		const data = JSON.parse(fromBase64(row.data_base64)) as Record<string, unknown>;
		map[tableName].push(data);
	}

	return map;
}

function sqlLiteral(value: unknown): string {
	if (value === null || value === undefined) return 'NULL';
	if (typeof value === 'number') return Number.isFinite(value) ? String(value) : 'NULL';
	if (typeof value === 'boolean') return value ? 'TRUE' : 'FALSE';
	const text = value instanceof Date ? value.toISOString() : String(value);
	return `'${text.replace(/'/g, "''")}'`;
}

function createSqlBackup(backupData: BackupDataMap): string {
	const lines: string[] = [];
	lines.push('-- zapp-backup v0.1.6');
	lines.push('BEGIN;');
	lines.push(
		`TRUNCATE TABLE ${tableConfigs.map((c) => `"${c.name}"`).join(', ')} RESTART IDENTITY CASCADE;`
	);

	for (const config of tableConfigs) {
		const rows = backupData[config.name];
		for (const originalRow of rows) {
			const row = prepareRowForInsert(originalRow, config.omitOnInsert);
			const columns = Object.keys(row);
			if (columns.length === 0) continue;
			const values = columns.map((col) => sqlLiteral(row[col]));
			lines.push(
				`INSERT INTO "${config.name}" (${columns.map((c) => `"${c}"`).join(', ')}) VALUES (${values.join(', ')});`
			);
		}
	}

	lines.push('COMMIT;');
	return `${lines.join('\n')}\n`;
}

async function processExportJob(jobId: number, format: string) {
	try {
		await db
			.update(exportJobs)
			.set({ status: 'processing' })
			.where(eq(exportJobs.id, jobId));

		const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
		const backupRows = await loadBackupRows();
		const backupData = backupRowsToMap(backupRows);

		if (format === 'csv') {
			const content = `table,data_base64\n${backupRows
				.map((row) => `${row.table},${row.data_base64}`)
				.join('\n')}\n`;

			await db
				.update(exportJobs)
				.set({
					status: 'completed',
					filename: `zakat-backup-${timestamp}.csv`,
					mimeType: 'text/csv; charset=utf-8',
					contentBase64: toBase64(content),
					completedAt: new Date()
				})
				.where(eq(exportJobs.id, jobId));
			return;
		}

		if (format === 'xlsx') {
			const worksheet = XLSX.utils.json_to_sheet(backupRows);
			const workbook = XLSX.utils.book_new();
			XLSX.utils.book_append_sheet(workbook, worksheet, 'backup');
			const buffer = XLSX.write(workbook, { type: 'buffer', bookType: 'xlsx' });

			await db
				.update(exportJobs)
				.set({
					status: 'completed',
					filename: `zakat-backup-${timestamp}.xlsx`,
					mimeType:
						'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					contentBase64: Buffer.from(buffer).toString('base64'),
					completedAt: new Date()
				})
				.where(eq(exportJobs.id, jobId));
			return;
		}

		if (format === 'sql') {
			const sqlContent = createSqlBackup(backupData);
			await db
				.update(exportJobs)
				.set({
					status: 'completed',
					filename: `zakat-backup-${timestamp}.sql`,
					mimeType: 'application/sql; charset=utf-8',
					contentBase64: toBase64(sqlContent),
					completedAt: new Date()
				})
				.where(eq(exportJobs.id, jobId));
			return;
		}

		await db
			.update(exportJobs)
			.set({ status: 'failed', error: 'Format export tidak didukung', completedAt: new Date() })
			.where(eq(exportJobs.id, jobId));
	} catch (error) {
		console.error('Export job failed:', error);
		await db
			.update(exportJobs)
			.set({ status: 'failed', error: 'Gagal export data', completedAt: new Date() })
			.where(eq(exportJobs.id, jobId));
	}
}

async function loadBackupRows(): Promise<BackupRow[]> {
	const backupRows: BackupRow[] = [];

	for (const config of tableConfigs) {
		const rows = await db.select().from(config.table);
		for (const row of rows as Record<string, unknown>[]) {
			const serialized = Object.fromEntries(
				Object.entries(row).map(([key, value]) => [key, serializeValue(value)])
			);

			backupRows.push({
				table: config.name,
				data_base64: toBase64(JSON.stringify(serialized))
			});
		}
	}

	return backupRows;
}

async function restoreBackup(backupData: BackupDataMap) {
	await db.transaction(async (tx) => {
		for (const config of tableConfigs) {
			await tx.delete(config.table);
		}

		for (const config of tableConfigs) {
			const rows = backupData[config.name];
			if (!rows || rows.length === 0) continue;

			const preparedRows = rows.map((row) => prepareRowForInsert(row, config.omitOnInsert));
			await tx.insert(config.table).values(preparedRows as never);
		}
	});
}

function detectImportFormat(filename: string): 'csv' | 'xlsx' | 'sql' | null {
	const lower = filename.toLowerCase();
	if (lower.endsWith('.csv')) return 'csv';
	if (lower.endsWith('.xlsx')) return 'xlsx';
	if (lower.endsWith('.sql')) return 'sql';
	return null;
}

export const load: PageServerLoad = async () => ({});

export const actions: Actions = {
	export: async ({ request, locals }) => {
		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}
		if (locals.user?.role !== 'admin') {
			return fail(403, { error: 'Forbidden' });
		}

		const formData = await request.formData();
		const format = String(formData.get('format') || 'csv').toLowerCase();
		const [job] = await db
			.insert(exportJobs)
			.values({
				status: 'pending',
				format,
				userId: locals.user?.id ?? ''
			})
			.returning({ id: exportJobs.id });

		if (!job?.id) {
			return fail(500, { error: 'Gagal membuat export job' });
		}

		void processExportJob(job.id, format);

		return {
			success: true,
			message: 'Export sedang diproses. Silakan tunggu.',
			jobId: job.id,
			status: 'pending' as ExportJobStatus
		};
	},
	exportStatus: async ({ request, locals }) => {
		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		const formData = await request.formData();
		const jobId = Number(formData.get('job_id'));

		if (!jobId || Number.isNaN(jobId)) {
			return fail(400, { error: 'ID job tidak valid' });
		}

		const [job] = await db
			.select()
			.from(exportJobs)
			.where(and(eq(exportJobs.id, jobId), eq(exportJobs.userId, locals.user?.id ?? '')))
			.orderBy(desc(exportJobs.createdAt))
			.limit(1);

		if (!job) {
			return fail(404, { error: 'Job export tidak ditemukan' });
		}

		return {
			success: true,
			status: job.status as ExportJobStatus,
			message: job.error || null,
			download: job.contentBase64
				? {
						filename: job.filename ?? 'zakat-backup',
						mimeType: job.mimeType ?? 'application/octet-stream',
						contentBase64: job.contentBase64
					}
				: null
		};
	},
	import: async ({ request, locals }) => {
		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}
		if (locals.user?.role !== 'admin') {
			return fail(403, { error: 'Forbidden' });
		}

		const formData = await request.formData();
		const file = formData.get('backup_file');

		if (!(file instanceof File)) {
			return fail(400, { error: 'File backup wajib dipilih' });
		}

		const format = detectImportFormat(file.name);
		if (!format) {
			return fail(400, { error: 'Format file tidak didukung. Gunakan .csv, .xlsx, atau .sql' });
		}

		try {
			if (format === 'sql') {
				const sqlContent = await file.text();
				if (!sqlContent.includes('-- zapp-backup v0.1.6')) {
					return fail(400, { error: 'File SQL bukan backup yang valid dari aplikasi ini' });
				}
				await db.execute(sql.raw(sqlContent));
				return { success: true, message: 'Import SQL berhasil. Database sudah direstore.' };
			}

			const backupRows =
				format === 'csv'
					? parseBackupRowsFromCsv(await file.text())
					: parseBackupRowsFromXlsx(Buffer.from(await file.arrayBuffer()));

			const backupData = backupRowsToMap(backupRows);
			await restoreBackup(backupData);

			return { success: true, message: `Import ${format.toUpperCase()} berhasil. Database sudah direstore.` };
		} catch (error) {
			console.error('Import backup failed:', error);
			return fail(500, { error: 'Gagal import backup. Pastikan file valid.' });
		}
	}
};
