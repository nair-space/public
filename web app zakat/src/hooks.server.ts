import { sequence } from '@sveltejs/kit/hooks';
import { building } from '$app/environment';
import { auth } from '$lib/server/auth.js';
import { svelteKitHandler } from 'better-auth/svelte-kit';
import type { Handle } from '@sveltejs/kit';
import { getTextDirection } from '$lib/paraglide/runtime.js';
import { paraglideMiddleware } from '$lib/paraglide/server.js';

const RATE_LIMIT_WINDOW_MS = 5 * 60 * 1000;
const RATE_LIMIT_MAX = 10;
const rateLimitMap = new Map<string, { count: number; firstRequest: number }>();

function isRateLimited(key: string): boolean {
	const now = Date.now();
	const entry = rateLimitMap.get(key);

	if (!entry) {
		rateLimitMap.set(key, { count: 1, firstRequest: now });
		return false;
	}

	if (now - entry.firstRequest > RATE_LIMIT_WINDOW_MS) {
		rateLimitMap.set(key, { count: 1, firstRequest: now });
		return false;
	}

	entry.count += 1;
	return entry.count > RATE_LIMIT_MAX;
}

const handleParaglide: Handle = ({ event, resolve }) =>
	paraglideMiddleware(event.request, ({ request, locale }) => {
		event.request = request;

		return resolve(event, {
			transformPageChunk: ({ html }) =>
				html
					.replace('%paraglide.lang%', locale)
					.replace('%paraglide.dir%', getTextDirection(locale))
		});
	});

const handleBetterAuth: Handle = async ({ event, resolve }) => {
	const url = new URL(event.request.url);
	if (event.request.method === 'POST' && url.pathname.startsWith('/api/auth/sign-in')) {
		const ip = event.getClientAddress();
		if (isRateLimited(`auth:${ip}`)) {
			return new Response('Too many login attempts. Please try again later.', { status: 429 });
		}
	}

	const session = await auth.api.getSession({ headers: event.request.headers });

	if (session) {
		event.locals.session = session.session;
		event.locals.user = session.user;
	}

	return svelteKitHandler({ event, resolve, auth, building });
};

export const handle: Handle = sequence(handleParaglide, handleBetterAuth);
