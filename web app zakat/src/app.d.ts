import type { User, Session } from 'better-auth/minimal';

declare module 'better-auth' {
	interface User {
		role?: string;
	}
}

// See https://svelte.dev/docs/kit/types#app.d.ts
declare global {
	namespace App {
		interface Locals {
			user?: User;
			session?: Session;
		}
	}
}

export {};
