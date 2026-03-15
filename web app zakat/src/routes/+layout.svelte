<script lang="ts">
	import { page } from '$app/state';
	import { locales, localizeHref } from '$lib/paraglide/runtime.js';
	import { resolve } from '$app/paths';
	import { injectSpeedInsights } from '@vercel/speed-insights/sveltekit';
	import './layout.css';

	const { children } = $props();

	injectSpeedInsights();
</script>

<svelte:head>
	<title>Zakat App — Management System</title>
	<meta name="description" content="Sistem Manajemen Zakat untuk pengelolaan Zakat Fitrah, Maal, Fidyah, Infaq, dan Shodaqoh" />
</svelte:head>

{@render children()}

<!-- Hidden locale links for paraglide -->
<div style="display:none">
	{#each locales as locale (locale)}
		<a href={resolve(localizeHref(page.url.pathname, { locale }) as '/')}>{locale}</a>
	{/each}
</div>
