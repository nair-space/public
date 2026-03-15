<script lang="ts">
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
	import GlassInput from '$lib/components/ui/GlassInput.svelte';
	import GlassButton from '$lib/components/ui/GlassButton.svelte';
	import { authClient } from '$lib/auth-client.js';
	import { goto } from '$app/navigation';
	import { resolve } from '$app/paths';

	let email = $state('');
	let password = $state('');
	let error = $state('');
	let loading = $state(false);

	async function handleSubmit(e: SubmitEvent) {
		e.preventDefault();
		error = '';
		loading = true;

		try {
			// Handle email conversion for login
			let loginEmail = email;
			if (!email.includes('@')) {
				// No @ at all, add @zakat.local (amil accounts)
				loginEmail = `${email}@zakat.local`;
			} else if (!email.includes('.')) {
				// Has @ but no dot (like "admin@ikhlash"), append .com
				loginEmail = `${email}.com`;
			}
			console.log('Login input:', email, '-> Converted to:', loginEmail);
			const result = await authClient.signIn.email({ email: loginEmail, password });
			if (result.error) {
				error = result.error.message ?? 'ID atau password salah.';
				loading = false;
				return;
			}
			await goto(resolve('/dashboard'));
		} catch {
			error = 'Terjadi kesalahan. Silakan coba lagi.';
		} finally {
			loading = false;
		}
	}
</script>

<svelte:head>
	<title>Login — Zakat App</title>
</svelte:head>

<div class="flex min-h-screen items-center justify-center px-4 py-8">
	<!-- Decorative blurs -->
	<div class="pointer-events-none fixed top-[-10%] left-[-5%] h-[400px] w-[400px] rounded-full bg-primary-600/20 blur-[120px]"></div>
	<div class="pointer-events-none fixed right-[-5%] bottom-[-10%] h-[350px] w-[350px] rounded-full bg-accent-500/15 blur-[100px]"></div>

	<div class="animate-fade-in w-full max-w-md">
		<!-- Logo -->
		<div class="mb-8 text-center">
			<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-linear-to-br from-primary-500 to-primary-700 text-2xl font-bold text-white shadow-xl shadow-primary-900/40">
				Z
			</div>
			<h1 class="text-2xl font-bold text-white">Zakat App</h1>
			<p class="mt-1 text-sm text-white/50">Sistem Manajemen Zakat</p>
		</div>

		<GlassCard>
			<h2 class="mb-6 text-center text-lg font-semibold text-white/90">
				Masuk ke Akun Anda
			</h2>

			<form onsubmit={handleSubmit} class="flex flex-col gap-4">
				<GlassInput
					label="ID"
					name="email"
					type="text"
					placeholder="Masukkan ID Anda"
					required
					bind:value={email}
				/>

				<GlassInput
					label="Password"
					name="password"
					type="password"
					placeholder="••••••••"
					required
					bind:value={password}
				/>

				{#if error}
					<div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400">
						{error}
					</div>
				{/if}

				<GlassButton type="submit" {loading} class="mt-2 w-full">
					Masuk
				</GlassButton>
			</form>
		</GlassCard>

		<p class="mt-6 text-center text-xs text-white/30">
			© 2026 Line Of Genesis. All rights reserved.
		</p>
	</div>
</div>
