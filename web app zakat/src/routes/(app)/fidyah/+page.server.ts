import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { fidyah } from '$lib/server/db/schema';
import { idNotaExists } from '$lib/server/db/nota';
import { applySummaryDelta, toNumber } from '$lib/server/db/summary';
import { and, desc, eq } from 'drizzle-orm';

const HISTORY_LIMIT = 100;

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.session) {
		return { fidyahList: [] };
	}

	const data = await db
		.select()
		.from(fidyah)
		.orderBy(desc(fidyah.createdAt))
		.limit(HISTORY_LIMIT);

	return { fidyahList: data };
};

export const actions: Actions = {
	save: async ({ request, locals }) => {
		const formData = await request.formData();

		const idNota = formData.get('id_nota') as string;
		const nama = formData.get('nama') as string;
		const fidyahUang = parseInt(formData.get('fidyah_uang') as string) || 0;
		const fidyahBeras = parseFloat(formData.get('fidyah_beras') as string) || 0;
		const entryCount = [fidyahUang, fidyahBeras].filter((value) => value > 0).length;

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
				.insert(fidyah)
				.values({
				nama,
				fidyahUangIncome: fidyahUang,
				fidyahBerasIncome: fidyahBeras.toString(),
				userId: locals.user?.id,
				...(idNota && idNota.trim() !== '' ? { idNotaInput: parseInt(idNota) } : {})
				})
				.returning({ createdAt: fidyah.createdAt });

			await applySummaryDelta(created?.createdAt ?? new Date(), {
				fidyahUangIncome: fidyahUang,
				fidyahBerasIncome: fidyahBeras
			});
		} catch (error) {
			console.error('Failed to save fidyah:', error);
			return fail(500, { error: 'Gagal menyimpan data' });
		}

		return { success: true };
	},
	update: async ({ request, locals }) => {
		const formData = await request.formData();

		const id = parseInt(formData.get('id') as string);
		const nama = formData.get('nama') as string;
		const fidyahUang = parseInt(formData.get('fidyah_uang') as string) || 0;
		const fidyahBeras = parseFloat(formData.get('fidyah_beras') as string) || 0;
		const entryCount = [fidyahUang, fidyahBeras].filter((value) => value > 0).length;

		if (!nama) {
			return fail(400, { error: 'Nama wajib diisi' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		try {
			const [existing] = await db
				.select()
				.from(fidyah)
				.where(and(eq(fidyah.id, id), eq(fidyah.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data fidyah tidak ditemukan' });
			}

			await db
				.update(fidyah)
				.set({
					nama,
					fidyahUangIncome: fidyahUang,
					fidyahBerasIncome: fidyahBeras.toString()
				})
				.where(and(eq(fidyah.id, id), eq(fidyah.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				fidyahUangIncome: fidyahUang - toNumber(existing.fidyahUangIncome),
				fidyahBerasIncome: fidyahBeras - toNumber(existing.fidyahBerasIncome)
			});
		} catch (error) {
			console.error('Failed to update fidyah:', error);
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
				.from(fidyah)
				.where(and(eq(fidyah.id, id), eq(fidyah.userId, locals.user?.id)))
				.limit(1);

			if (!existing) {
				return fail(404, { error: 'Data fidyah tidak ditemukan' });
			}

			await db
				.delete(fidyah)
				.where(and(eq(fidyah.id, id), eq(fidyah.userId, locals.user?.id)));

			await applySummaryDelta(existing.createdAt, {
				fidyahUangIncome: -toNumber(existing.fidyahUangIncome),
				fidyahBerasIncome: -toNumber(existing.fidyahBerasIncome)
			});
		} catch (error) {
			console.error('Failed to delete fidyah:', error);
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
