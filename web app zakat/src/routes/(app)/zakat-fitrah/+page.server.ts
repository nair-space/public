import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { zakatFitrah } from '$lib/server/db/schema';
import { idNotaExists } from '$lib/server/db/nota';
import { applySummaryDelta, toNumber } from '$lib/server/db/summary';
import { and, desc, eq } from 'drizzle-orm';

const HISTORY_LIMIT = 100;

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.session) {
		return { zakatFitrahList: [] };
	}

	const data = await db
		.select()
		.from(zakatFitrah)
		.orderBy(desc(zakatFitrah.createdAt))
		.limit(HISTORY_LIMIT);

	return { zakatFitrahList: data };
};

export const actions: Actions = {
	save: async ({ request, locals }) => {
		const formData = await request.formData();

		const idNota = formData.get('id_nota') as string;
		const namaMuzakki = formData.get('nama_muzakki') as string;
		const jumlahTanggungan = parseInt(formData.get('jumlah_tanggungan') as string) || 0;
		const zakatUang = parseInt(formData.get('zakat_uang') as string) || 0;
		const zakatBeras = parseFloat(formData.get('zakat_beras') as string) || 0;
		const entryCount = [zakatUang, zakatBeras].filter((value) => value > 0).length;

		if (!namaMuzakki) {
			return fail(400, { error: 'Nama muzakki wajib diisi' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}
		if (entryCount !== 1) {
			return fail(400, { error: 'pastikan hanya input satu data saja' });
		}

		// Check for duplicate id_nota if provided
		if (idNota && idNota.trim() !== '') {
			const idNotaNum = parseInt(idNota);
			const exists = await idNotaExists(idNotaNum);
			if (exists) {
				return fail(400, { error: 'id nota ini sudah dipakai' });
			}
		}

		try {
			const [created] = await db
				.insert(zakatFitrah)
				.values({
				namaMuzakki,
				jumlahTanggungan,
				zakatFitrahUangIncome: zakatUang,
				zakatFitrahBerasIncome: zakatBeras.toString(),
				userId: locals.user?.id,
				...(idNota && idNota.trim() !== '' ? { idNotaInput: parseInt(idNota) } : {})
				})
				.returning({ createdAt: zakatFitrah.createdAt });

			await applySummaryDelta(created?.createdAt ?? new Date(), {
				fitrahUangIncome: zakatUang,
				fitrahBerasIncome: zakatBeras
			});
		} catch (error) {
			console.error('Failed to save zakat fitrah:', error);
			return fail(500, { error: 'Gagal menyimpan data' });
		}

		return { success: true };
	},
	update: async ({ request, locals }) => {
		const formData = await request.formData();

		const id = parseInt(formData.get('id') as string);
		const namaMuzakki = formData.get('nama_muzakki') as string;
		const jumlahTanggungan = parseInt(formData.get('jumlah_tanggungan') as string) || 0;
		const zakatUang = parseInt(formData.get('zakat_uang') as string) || 0;
		const zakatBeras = parseFloat(formData.get('zakat_beras') as string) || 0;
		const entryCount = [zakatUang, zakatBeras].filter((value) => value > 0).length;

		if (!namaMuzakki) {
			return fail(400, { error: 'Nama muzakki wajib diisi' });
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
				.from(zakatFitrah)
				.where(and(eq(zakatFitrah.id, id), eq(zakatFitrah.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data zakat fitrah tidak ditemukan' });
			}

			await db
				.update(zakatFitrah)
				.set({
					namaMuzakki,
					jumlahTanggungan,
					zakatFitrahUangIncome: zakatUang,
					zakatFitrahBerasIncome: zakatBeras.toString()
				})
				.where(and(eq(zakatFitrah.id, id), eq(zakatFitrah.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				fitrahUangIncome: zakatUang - toNumber(existing.zakatFitrahUangIncome),
				fitrahBerasIncome: zakatBeras - toNumber(existing.zakatFitrahBerasIncome)
			});
		} catch (error) {
			console.error('Failed to update zakat fitrah:', error);
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
				.from(zakatFitrah)
				.where(and(eq(zakatFitrah.id, id), eq(zakatFitrah.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data zakat fitrah tidak ditemukan' });
			}

			await db
				.delete(zakatFitrah)
				.where(and(eq(zakatFitrah.id, id), eq(zakatFitrah.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				fitrahUangIncome: -toNumber(existing.zakatFitrahUangIncome),
				fitrahBerasIncome: -toNumber(existing.zakatFitrahBerasIncome)
			});
		} catch (error) {
			console.error('Failed to delete zakat fitrah:', error);
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
		const exists = await idNotaExists(idNotaNum);

		return { exists };
	}
};
