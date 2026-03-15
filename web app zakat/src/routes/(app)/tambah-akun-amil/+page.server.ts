import type { Actions, PageServerLoad } from './$types';
import { fail, redirect } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { user, account } from '$lib/server/db/schema';
import { eq } from 'drizzle-orm';
import { hash } from 'bcryptjs';

// Password hash function
async function hashPassword(password: string): Promise<string> {
	return await hash(password, 10);
}

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.user) {
		throw redirect(302, '/login');
	}

	// Only admin can access this page
	const isAdmin = locals.user.email?.includes('admin@ikhlash') || locals.user.role === 'admin';
	if (!isAdmin) {
		throw redirect(302, '/dashboard');
	}

	// Fetch all amil accounts (exclude admin)
	const amilList = await db
		.select({ id: user.id, name: user.name, email: user.email })
		.from(user)
		.where(eq(user.role, 'amil'));

	return { amilList };
};

export const actions: Actions = {
	create: async ({ request, locals }) => {
		if (!locals.user) {
			return fail(401, { error: 'Unauthorized' });
		}

		// Only admin can create amil accounts
		const isAdmin = locals.user.email?.includes('admin@ikhlash') || locals.user.role === 'admin';
		if (!isAdmin) {
			return fail(403, { error: 'Hanya admin yang dapat membuat akun amil' });
		}

		const formData = await request.formData();
		const namaAmil = formData.get('nama_amil') as string;
		const idAmil = formData.get('id_amil') as string;
		const password = formData.get('password') as string;

		if (!namaAmil || !idAmil || !password) {
			return fail(400, { error: 'Semua field wajib diisi' });
		}

		// Check if user already exists
		const existingUser = await db.select().from(user).where(eq(user.id, idAmil)).limit(1);
		if (existingUser.length > 0) {
			return fail(400, { error: 'ID ini sudah dipakai' });
		}

		try {
			// Hash password using bcrypt
			const hashedPassword = await hashPassword(password);

			// Create user directly in database (no session created)
			await db.insert(user).values({
				id: idAmil,
				name: namaAmil,
				email: `${idAmil}@zakat.local`,
				role: 'amil',
				emailVerified: true
			});

			// Create account with hashed password
			await db.insert(account).values({
				id: crypto.randomUUID(),
				accountId: idAmil,
				providerId: 'credential',
				userId: idAmil,
				password: hashedPassword
			});
		} catch (error) {
			console.error('Failed to create amil account:', error);
			return fail(500, { error: 'Gagal membuat akun amil' });
		}

		return { success: true };
	},

	checkId: async ({ request }) => {
		const formData = await request.formData();
		const id = formData.get('id') as string;

		if (!id || id.trim() === '') {
			return { exists: false };
		}

		// Check if ID exists in user table
		const existingUser = await db.select().from(user).where(eq(user.id, id)).limit(1);
		const exists = existingUser.length > 0;

		return { exists };
	},

	resetPassword: async ({ request, locals }) => {
		if (!locals.user) {
			return fail(401, { error: 'Unauthorized' });
		}

		// Only admin can reset passwords
		const isAdmin = locals.user.email?.includes('admin@ikhlash') || locals.user.role === 'admin';
		if (!isAdmin) {
			return fail(403, { error: 'Hanya admin yang dapat mengganti password' });
		}

		const formData = await request.formData();
		const userId = formData.get('user_id') as string;
		const newPassword = formData.get('new_password') as string;

		if (!userId || !newPassword) {
			return fail(400, { error: 'User ID dan password baru wajib diisi' });
		}

		try {
			// Hash new password using bcrypt
			const hashedPassword = await hashPassword(newPassword);

			// Update password in account table
			await db.update(account)
				.set({ password: hashedPassword })
				.where(eq(account.userId, userId));
		} catch (error) {
			console.error('Failed to reset password:', error);
			return fail(500, { error: 'Gagal mengganti password' });
		}

		return { success: true, message: 'Password berhasil diganti' };
	},

	delete: async ({ request, locals }) => {
		if (!locals.user) {
			return fail(401, { error: 'Unauthorized' });
		}

		// Only admin can delete accounts
		const isAdmin = locals.user.email?.includes('admin@ikhlash') || locals.user.role === 'admin';
		if (!isAdmin) {
			return fail(403, { error: 'Hanya admin yang dapat menghapus akun' });
		}

		const formData = await request.formData();
		const userId = formData.get('user_id') as string;

		if (!userId) {
			return fail(400, { error: 'User ID wajib diisi' });
		}

		try {
			// Delete account first (foreign key constraint)
			await db.delete(account).where(eq(account.userId, userId));

			// Delete user
			await db.delete(user).where(eq(user.id, userId));
		} catch (error) {
			console.error('Failed to delete account:', error);
			return fail(500, { error: 'Gagal menghapus akun' });
		}

		return { success: true, message: 'Akun berhasil dihapus' };
	}
};
