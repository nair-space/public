<script lang="ts">
	import "./layout.css";
	import "../app.css";
	import Header from "$lib/components/Header.svelte";

	const { children } = $props();
</script>

<Header />

{@render children()}
