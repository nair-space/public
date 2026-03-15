import { deLocalizeUrl } from '$lib/paraglide/runtime.js';
import type { RequestEvent } from '@sveltejs/kit';

export const reroute = (request: Pick<RequestEvent, 'url'>) => deLocalizeUrl(request.url).pathname;
