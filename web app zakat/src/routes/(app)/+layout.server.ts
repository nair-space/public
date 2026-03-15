import { redirect } from '@sveltejs/kit';
import type { LayoutServerLoad } from './$types';

export const load: LayoutServerLoad = async ({ locals }) => {
	if (!locals.user) {
		throw redirect(302, '/login');
	}

	// Check if admin by email pattern (admin@ikhlash@zakat.local or contains admin@ikhlash)
	const isAdmin = locals.user.email?.includes('admin@ikhlash') || locals.user.role === 'admin';

	return {
		user: {
			id: locals.user.id,
			name: locals.user.name,
			email: locals.user.email,
			role: locals.user.role || (isAdmin ? 'admin' : 'amil')
		}
	};
};
