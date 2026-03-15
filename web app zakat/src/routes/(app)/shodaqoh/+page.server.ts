import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { shodaqoh } from '$lib/server/db/schema';
import { idNotaExists } from '$lib/server/db/nota';
import { applySummaryDelta, toNumber } from '$lib/server/db/summary';
import { and, desc, eq } from 'drizzle-orm';

const HISTORY_LIMIT = 100;

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.session) {
		return { shodaqohList: [] };
	}

	const data = await db
		.select()
		.from(shodaqoh)
		.orderBy(desc(shodaqoh.createdAt))
		.limit(HISTORY_LIMIT);

	return { shodaqohList: data };
};

export const actions: Actions = {
	save: async ({ request, locals }) => {
		const formData = await request.formData();

		const idNota = formData.get('id_nota') as string;
		const nama = formData.get('nama') as string;
		const shodaqohUang = parseInt(formData.get('shodaqoh_uang') as string) || 0;

		if (!nama) {
			return fail(400, { error: 'Nama wajib diisi' });
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
				.insert(shodaqoh)
				.values({
				nama,
				shodaqohUangIncome: shodaqohUang,
				userId: locals.user?.id,
				...(idNota && idNota.trim() !== '' ? { idNotaInput: parseInt(idNota) } : {})
				})
				.returning({ createdAt: shodaqoh.createdAt });

			await applySummaryDelta(created?.createdAt ?? new Date(), {
				shodaqohUangIncome: shodaqohUang
			});
		} catch (error) {
			console.error('Failed to save shodaqoh:', error);
			return fail(500, { error: 'Gagal menyimpan data' });
		}

		return { success: true };
	},
	update: async ({ request, locals }) => {
		const formData = await request.formData();

		const id = parseInt(formData.get('id') as string);
		const nama = formData.get('nama') as string;
		const shodaqohUang = parseInt(formData.get('shodaqoh_uang') as string) || 0;

		if (!nama) {
			return fail(400, { error: 'Nama wajib diisi' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		try {
			const [existing] = await db
				.select()
				.from(shodaqoh)
				.where(and(eq(shodaqoh.id, id), eq(shodaqoh.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data shodaqoh tidak ditemukan' });
			}

			await db
				.update(shodaqoh)
				.set({
					nama,
					shodaqohUangIncome: shodaqohUang
				})
				.where(and(eq(shodaqoh.id, id), eq(shodaqoh.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				shodaqohUangIncome: shodaqohUang - toNumber(existing.shodaqohUangIncome)
			});
		} catch (error) {
			console.error('Failed to update shodaqoh:', error);
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
				.from(shodaqoh)
				.where(and(eq(shodaqoh.id, id), eq(shodaqoh.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data shodaqoh tidak ditemukan' });
			}

			await db
				.delete(shodaqoh)
				.where(and(eq(shodaqoh.id, id), eq(shodaqoh.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				shodaqohUangIncome: -toNumber(existing.shodaqohUangIncome)
			});
		} catch (error) {
			console.error('Failed to delete shodaqoh:', error);
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
