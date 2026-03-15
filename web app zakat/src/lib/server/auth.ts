import { betterAuth } from 'better-auth/minimal';
import { drizzleAdapter } from 'better-auth/adapters/drizzle';
import { sveltekitCookies } from 'better-auth/svelte-kit';
import { env } from '$env/dynamic/private';
import { getRequestEvent } from '$app/server';
import { db } from '$lib/server/db';
import { hash, compare } from 'bcryptjs';

import { admin } from 'better-auth/plugins';

export const auth = betterAuth({
	baseURL: env.ORIGIN,
	secret: env.BETTER_AUTH_SECRET,
	database: drizzleAdapter(db, { provider: 'pg' }),
	emailAndPassword: {
		enabled: true,
		password: {
			hash: async (password) => {
				return await hash(password, 10);
			},
			verify: async ({ hash: hashValue, password }) => {
				return await compare(password, hashValue);
			}
		}
	},
	plugins: [admin(), sveltekitCookies(getRequestEvent)], // make sure this is the last plugin in the array,
	additionalFields: {
		role: {
			type: 'string',
			defaultValue: 'amil'
		}
	}
});
