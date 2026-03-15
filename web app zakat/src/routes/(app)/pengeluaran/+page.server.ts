import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { pengeluaran } from '$lib/server/db/schema';
import { applySummaryDelta, toNumber } from '$lib/server/db/summary';
import { and, desc, eq } from 'drizzle-orm';

const HISTORY_LIMIT = 100;

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.session) {
		return { pengeluaranList: [] };
	}

	const data = await db
		.select()
		.from(pengeluaran)
		.orderBy(desc(pengeluaran.createdAt))
		.limit(HISTORY_LIMIT);

	return { pengeluaranList: data };
};

export const actions: Actions = {
	save: async ({ request, locals }) => {
		const formData = await request.formData();

		const idNota = formData.get('id_nota') as string;
		const namaPengeluaran = formData.get('nama_pengeluaran') as string;
		const zakatFitrahUang = parseInt(formData.get('zakat_fitrah_uang_outcome') as string) || 0;
		const zakatFitrahBeras = parseFloat(formData.get('zakat_fitrah_beras_outcome') as string) || 0;
		const zakatMaalUang = parseInt(formData.get('zakat_maal_uang_outcome') as string) || 0;
		const fidyahUang = parseInt(formData.get('fidyah_uang_outcome') as string) || 0;
		const fidyahBeras = parseFloat(formData.get('fidyah_beras_outcome') as string) || 0;
		const infaqUang = parseInt(formData.get('infaq_uang_outcome') as string) || 0;
		const shodaqohUang = parseInt(formData.get('shodaqoh_uang_outcome') as string) || 0;
		const entryCount = [
			zakatFitrahUang,
			zakatFitrahBeras,
			zakatMaalUang,
			fidyahUang,
			fidyahBeras,
			infaqUang,
			shodaqohUang
		].filter((value) => value > 0).length;

		// Calculate jumlah pengeluaran automatically
		const jumlahPengeluaran = zakatFitrahUang + zakatMaalUang + fidyahUang + infaqUang + shodaqohUang;
		const jumlahPengeluaranBeras = zakatFitrahBeras + fidyahBeras;

		if (!namaPengeluaran) {
			return fail(400, { error: 'Nama pengeluaran wajib diisi' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}
		if (entryCount !== 1) {
			return fail(400, { error: 'pastikan hanya input satu data saja' });
		}

		// Check for duplicate id_nota within pengeluaran table only
		if (idNota && idNota.trim() !== '') {
			const idNotaNum = parseInt(idNota);

			const existingPengeluaran = await db
				.select()
				.from(pengeluaran)
				.where(eq(pengeluaran.idNotaInput, idNotaNum))
				.limit(1);

			if (existingPengeluaran.length > 0) {
				return fail(400, { error: 'id nota ini sudah dipakai' });
			}
		}

		try {
			const [created] = await db
				.insert(pengeluaran)
				.values({
				namaPengeluaran,
				jumlahPengeluaran,
				jumlahPengeluaranBeras: jumlahPengeluaranBeras.toString(),
				zakatFitrahUangOutcome: zakatFitrahUang,
				zakatFitrahBerasOutcome: zakatFitrahBeras.toString(),
				zakatMaalUangOutcome: zakatMaalUang,
				fidyahUangOutcome: fidyahUang,
				fidyahBerasOutcome: fidyahBeras.toString(),
				infaqUangOutcome: infaqUang,
				shodaqohUangOutcome: shodaqohUang,
				userId: locals.user?.id,
				...(idNota && idNota.trim() !== '' ? { idNotaInput: parseInt(idNota) } : {})
				})
				.returning({ createdAt: pengeluaran.createdAt });

			await applySummaryDelta(created?.createdAt ?? new Date(), {
				fitrahUangOutcome: zakatFitrahUang,
				fitrahBerasOutcome: zakatFitrahBeras,
				maalUangOutcome: zakatMaalUang,
				fidyahUangOutcome: fidyahUang,
				fidyahBerasOutcome: fidyahBeras,
				infaqUangOutcome: infaqUang,
				shodaqohUangOutcome: shodaqohUang
			});
		} catch (error) {
			console.error('Failed to save pengeluaran:', error);
			return fail(500, { error: 'Gagal menyimpan data' });
		}

		return { success: true };
	},
	update: async ({ request, locals }) => {
		const formData = await request.formData();

		const id = parseInt(formData.get('id') as string);
		const namaPengeluaran = formData.get('nama_pengeluaran') as string;
		const zakatFitrahUang = parseInt(formData.get('zakat_fitrah_uang_outcome') as string) || 0;
		const zakatFitrahBeras = parseFloat(formData.get('zakat_fitrah_beras_outcome') as string) || 0;
		const zakatMaalUang = parseInt(formData.get('zakat_maal_uang_outcome') as string) || 0;
		const fidyahUang = parseInt(formData.get('fidyah_uang_outcome') as string) || 0;
		const fidyahBeras = parseFloat(formData.get('fidyah_beras_outcome') as string) || 0;
		const infaqUang = parseInt(formData.get('infaq_uang_outcome') as string) || 0;
		const shodaqohUang = parseInt(formData.get('shodaqoh_uang_outcome') as string) || 0;
		const entryCount = [
			zakatFitrahUang,
			zakatFitrahBeras,
			zakatMaalUang,
			fidyahUang,
			fidyahBeras,
			infaqUang,
			shodaqohUang
		].filter((value) => value > 0).length;

		// Calculate jumlah pengeluaran automatically
		const jumlahPengeluaran = zakatFitrahUang + zakatMaalUang + fidyahUang + infaqUang + shodaqohUang;
		const jumlahPengeluaranBeras = zakatFitrahBeras + fidyahBeras;

		if (!namaPengeluaran) {
			return fail(400, { error: 'Nama pengeluaran wajib diisi' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}
		if (entryCount !== 1) {
			return fail(400, { error: 'pastikan hanya input satu data saja' });
		}

		try {
			const [existing] = await db
				.select()
				.from(pengeluaran)
				.where(and(eq(pengeluaran.id, id), eq(pengeluaran.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data pengeluaran tidak ditemukan' });
			}

			await db
				.update(pengeluaran)
				.set({
					namaPengeluaran,
					jumlahPengeluaran,
					jumlahPengeluaranBeras: jumlahPengeluaranBeras.toString(),
					zakatFitrahUangOutcome: zakatFitrahUang,
					zakatFitrahBerasOutcome: zakatFitrahBeras.toString(),
					zakatMaalUangOutcome: zakatMaalUang,
					fidyahUangOutcome: fidyahUang,
					fidyahBerasOutcome: fidyahBeras.toString(),
					infaqUangOutcome: infaqUang,
					shodaqohUangOutcome: shodaqohUang
				})
				.where(and(eq(pengeluaran.id, id), eq(pengeluaran.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				fitrahUangOutcome: zakatFitrahUang - toNumber(existing.zakatFitrahUangOutcome),
				fitrahBerasOutcome: zakatFitrahBeras - toNumber(existing.zakatFitrahBerasOutcome),
				maalUangOutcome: zakatMaalUang - toNumber(existing.zakatMaalUangOutcome),
				fidyahUangOutcome: fidyahUang - toNumber(existing.fidyahUangOutcome),
				fidyahBerasOutcome: fidyahBeras - toNumber(existing.fidyahBerasOutcome),
				infaqUangOutcome: infaqUang - toNumber(existing.infaqUangOutcome),
				shodaqohUangOutcome: shodaqohUang - toNumber(existing.shodaqohUangOutcome)
			});
		} catch (error) {
			console.error('Failed to update pengeluaran:', error);
			return fail(500, { error: 'Gagal mengupdate data' });
		}

		return { success: true };
	},
	delete: async ({ request, locals }) => {
		const formData = await request.formData();
		const id = parseInt(formData.get('id') as string);

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		if (!id || Number.isNaN(id)) {
			return fail(400, { error: 'ID tidak valid' });
		}

		try {
			const [existing] = await db
				.select()
				.from(pengeluaran)
				.where(and(eq(pengeluaran.id, id), eq(pengeluaran.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data pengeluaran tidak ditemukan' });
			}

			await db
				.delete(pengeluaran)
				.where(and(eq(pengeluaran.id, id), eq(pengeluaran.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				fitrahUangOutcome: -toNumber(existing.zakatFitrahUangOutcome),
				fitrahBerasOutcome: -toNumber(existing.zakatFitrahBerasOutcome),
				maalUangOutcome: -toNumber(existing.zakatMaalUangOutcome),
				fidyahUangOutcome: -toNumber(existing.fidyahUangOutcome),
				fidyahBerasOutcome: -toNumber(existing.fidyahBerasOutcome),
				infaqUangOutcome: -toNumber(existing.infaqUangOutcome),
				shodaqohUangOutcome: -toNumber(existing.shodaqohUangOutcome)
			});
		} catch (error) {
			console.error('Failed to delete pengeluaran:', error);
			return fail(500, { error: 'Gagal menghapus data' });
		}

		return { success: true };
	},
	checkIdNota: async ({ request }) => {
		const formData = await request.formData();
		const idNota = formData.get('id_nota') as string;

		if (!idNota || idNota.trim() === '') {
			return { exists: false };
		}

		const idNotaNum = parseInt(idNota);

		// Only check pengeluaran table since it uses separate sequence
		const existingPengeluaran = await db
			.select()
			.from(pengeluaran)
			.where(eq(pengeluaran.idNotaInput, idNotaNum))
			.limit(1);

		const exists = existingPengeluaran.length > 0;

		return { exists };
	}
};
