import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { amil, amilSettings } from '$lib/server/db/schema';
import { desc, eq, sum } from 'drizzle-orm';

const HISTORY_LIMIT = 100;

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.session) {
		return { amilList: [], settings: null };
	}

	const [settings] = await db.select().from(amilSettings).where(eq(amilSettings.id, 1));
	const data = await db
		.select()
		.from(amil)
		.orderBy(desc(amil.createdAt))
		.limit(HISTORY_LIMIT);
	return { amilList: data, settings };
};

export const actions: Actions = {
	save: async ({ request, locals }) => {
		const formData = await request.formData();

		const namaAmil = formData.get('nama_amil') as string;
		const absenAmil = parseInt(formData.get('absen_amil') as string) || 1;

		if (!namaAmil) {
			return fail(400, { error: 'Nama amil wajib diisi' });
		}

		if (absenAmil < 1 || absenAmil > 5) {
			return fail(400, { error: 'Absen amil harus antara 1-5' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		// Get global settings
		const settings = await db.select().from(amilSettings).where(eq(amilSettings.id, 1)).limit(1);
		let feeDasar = settings[0]?.feeDasar || 0;
		let jatahAmil = settings[0]?.jatahAmil || 0;

		// If settings don't exist, create with form values or defaults
		if (!settings[0]) {
			const formFeeDasar = parseInt(formData.get('fee_dasar') as string) || 0;
			const formJatah = parseInt(formData.get('jatah_amil') as string) || 0;
			feeDasar = formFeeDasar;
			jatahAmil = formJatah;

			await db.insert(amilSettings).values({
				id: 1,
				feeDasar,
				jatahAmil
			});
		}

		// Calculate fee total using global fee_dasar
		const feeTotalAmil = absenAmil * feeDasar;

		// Check if total fee of all amil exceeds jatah_amil
		const [totalResult] = await db.select({ total: sum(amil.feeTotalAmil) }).from(amil);
		const currentTotalFee = parseInt(totalResult?.total || '0');
		const newTotalFee = currentTotalFee + feeTotalAmil;

		if (newTotalFee > jatahAmil && jatahAmil > 0) {
			return fail(400, { error: 'fee amil melewati jatah amil' });
		}

		try {
			await db.insert(amil).values({
				namaAmil,
				absenAmil,
				feeTotalAmil,
				userId: locals.user?.id
			});

			return { success: true };
		} catch (err) {
			console.error('Failed to save amil:', err);
			return fail(500, { error: 'Gagal menyimpan data amil' });
		}
	},

	update: async ({ request, locals }) => {
		const formData = await request.formData();

		const id = parseInt(formData.get('id') as string);
		const namaAmil = formData.get('nama_amil') as string;
		const absenAmil = parseInt(formData.get('absen_amil') as string) || 1;

		if (!namaAmil) {
			return fail(400, { error: 'Nama amil wajib diisi' });
		}

		if (absenAmil < 1 || absenAmil > 5) {
			return fail(400, { error: 'Absen amil harus antara 1-5' });
		}

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		// Get global settings
		const [settings] = await db.select().from(amilSettings).where(eq(amilSettings.id, 1)).limit(1);
		const feeDasar = settings?.feeDasar || 0;
		const jatahAmil = settings?.jatahAmil || 0;

		// Calculate fee total using global fee_dasar
		const feeTotalAmil = absenAmil * feeDasar;

		// Get current amil record to subtract old fee_total
		const [existingAmil] = await db.select().from(amil).where(eq(amil.id, id)).limit(1);
		if (!existingAmil) {
			return fail(404, { error: 'Data amil tidak ditemukan' });
		}

		// Check if total fee of all amil (minus old fee, plus new fee) exceeds jatah_amil
		const [totalResult] = await db.select({ total: sum(amil.feeTotalAmil) }).from(amil);
		const currentTotalFee = parseInt(totalResult?.total || '0');
		const newTotalFee = currentTotalFee - existingAmil.feeTotalAmil + feeTotalAmil;

		if (newTotalFee > jatahAmil && jatahAmil > 0) {
			return fail(400, { error: 'fee amil melewati jatah amil' });
		}

		try {
			await db
				.update(amil)
				.set({
					namaAmil,
					absenAmil,
					feeTotalAmil
				})
				.where(eq(amil.id, id));

			return { success: true };
		} catch (err) {
			console.error('Failed to update amil:', err);
			return fail(500, { error: 'Gagal mengupdate data amil' });
		}
	},

	updateSettings: async ({ request, locals }) => {
		const formData = await request.formData();

		const feeDasar = parseInt(formData.get('fee_dasar') as string) || 0;
		const jatahAmil = parseInt(formData.get('jatah_amil') as string) || 0;

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}
		if (locals.user?.role !== 'admin') {
			return fail(403, { error: 'Forbidden' });
		}

		try {
			// Update or insert settings
			const [existing] = await db.select().from(amilSettings).where(eq(amilSettings.id, 1)).limit(1);

			if (existing) {
				await db
					.update(amilSettings)
					.set({ feeDasar, jatahAmil, updatedAt: new Date() })
					.where(eq(amilSettings.id, 1));
			} else {
				await db.insert(amilSettings).values({
					id: 1,
					feeDasar,
					jatahAmil
				});
			}

			// Recalculate all amil fees with new fee_dasar
			const allAmil = await db.select().from(amil);
			for (const a of allAmil) {
				const newFeeTotal = a.absenAmil * feeDasar;
				await db
					.update(amil)
					.set({ feeTotalAmil: newFeeTotal })
					.where(eq(amil.id, a.id));
			}

			return { success: true };
		} catch (err) {
			console.error('Failed to update settings:', err);
			return fail(500, { error: 'Gagal mengupdate pengaturan' });
		}
	},

	delete: async ({ request, locals }) => {
		const formData = await request.formData();
		const id = parseInt(formData.get('id') as string);

		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		try {
			await db.delete(amil).where(eq(amil.id, id));
			return { success: true };
		} catch (err) {
			console.error('Failed to delete amil:', err);
			return fail(500, { error: 'Gagal menghapus data amil' });
		}
	}
};
