import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { zakatMaal } from '$lib/server/db/schema';
import { idNotaExists } from '$lib/server/db/nota';
import { applySummaryDelta, toNumber } from '$lib/server/db/summary';
import { and, desc, eq } from 'drizzle-orm';

const HISTORY_LIMIT = 100;

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.session) {
		return { zakatMaalList: [] };
	}

	const data = await db
		.select()
		.from(zakatMaal)
		.orderBy(desc(zakatMaal.createdAt))
		.limit(HISTORY_LIMIT);

	return { zakatMaalList: data };
};

export const actions: Actions = {
	save: async ({ request, locals }) => {
		const formData = await request.formData();

		const idNota = formData.get('id_nota') as string;
		const namaMuzakki = formData.get('nama_muzakki') as string;
		const zakatMaalUang = parseInt(formData.get('zakat_maal_uang') as string) || 0;

		if (!namaMuzakki) {
			return fail(400, { error: 'Nama muzakki wajib diisi' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
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
				.insert(zakatMaal)
				.values({
				namaMuzakki,
				zakatMaalUangIncome: zakatMaalUang,
				userId: locals.user?.id,
				...(idNota && idNota.trim() !== '' ? { idNotaInput: parseInt(idNota) } : {})
				})
				.returning({ createdAt: zakatMaal.createdAt });

			await applySummaryDelta(created?.createdAt ?? new Date(), {
				maalUangIncome: zakatMaalUang
			});
		} catch (error) {
			console.error('Failed to save zakat maal:', error);
			return fail(500, { error: 'Gagal menyimpan data' });
		}

		return { success: true };
	},
	update: async ({ request, locals }) => {
		const formData = await request.formData();

		const id = parseInt(formData.get('id') as string);
		const namaMuzakki = formData.get('nama_muzakki') as string;
		const zakatMaalUang = parseInt(formData.get('zakat_maal_uang') as string) || 0;

		if (!namaMuzakki) {
			return fail(400, { error: 'Nama muzakki wajib diisi' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		try {
			const [existing] = await db
				.select()
				.from(zakatMaal)
				.where(and(eq(zakatMaal.id, id), eq(zakatMaal.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data zakat maal tidak ditemukan' });
			}

			await db
				.update(zakatMaal)
				.set({
					namaMuzakki,
					zakatMaalUangIncome: zakatMaalUang
				})
				.where(and(eq(zakatMaal.id, id), eq(zakatMaal.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				maalUangIncome: zakatMaalUang - toNumber(existing.zakatMaalUangIncome)
			});
		} catch (error) {
			console.error('Failed to update zakat maal:', error);
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
				.from(zakatMaal)
				.where(and(eq(zakatMaal.id, id), eq(zakatMaal.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data zakat maal tidak ditemukan' });
			}

			await db
				.delete(zakatMaal)
				.where(and(eq(zakatMaal.id, id), eq(zakatMaal.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				maalUangIncome: -toNumber(existing.zakatMaalUangIncome)
			});
		} catch (error) {
			console.error('Failed to delete zakat maal:', error);
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
