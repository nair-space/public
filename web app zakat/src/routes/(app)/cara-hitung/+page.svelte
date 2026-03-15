<script lang="ts">
	import GlassCard from '$lib/components/ui/GlassCard.svelte';
</script>

<svelte:head>
	<title>Cara Hitung — Zakat App</title>
</svelte:head>

<div class="space-y-6">
	<GlassCard title="Cara Menghitung Zakat">
		<div class="space-y-4 text-sm text-white/70">
			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<h4 class="mb-2 font-semibold text-white/90">🌾 Zakat Fitrah</h4>
				<p>Zakat Fitrah berupa uang atau beras yang wajib dikeluarkan setiap jiwa menjelang Idul Fitri.</p>
				<ul class="mt-2 ml-4 list-disc space-y-1 text-white/50">
					<li>Uang: Sesuai harga beras per jiwa</li>
					<li>Beras: 2.5 kg / 3.5 liter per jiwa</li>
				</ul>
			</div>

			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<h4 class="mb-2 font-semibold text-white/90">💰 Zakat Maal</h4>
				<p>Zakat harta yang telah mencapai nisab dan haul.</p>
				<ul class="mt-2 ml-4 list-disc space-y-1 text-white/50">
					<li>Nisab: Setara 85 gram emas</li>
					<li>Tarif: 2.5% dari total harta</li>
				</ul>
			</div>

			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<h4 class="mb-2 font-semibold text-white/90">🍚 Fidyah</h4>
				<p>Pengganti puasa bagi yang tidak mampu berpuasa.</p>
				<ul class="mt-2 ml-4 list-disc space-y-1 text-white/50">
					<li>1 mud makanan pokok per hari</li>
					<li>Setara ±750 gram beras per hari</li>
				</ul>
			</div>

			<div class="rounded-xl border border-white/10 bg-white/5 p-4">
				<h4 class="mb-2 font-semibold text-white/90">🤲 Infaq & 💝 Shodaqoh</h4>
				<p>Sumbangan sukarela yang tidak memiliki batasan nisab maupun haul.</p>
			</div>

			<div class="rounded-xl border border-primary-500/20 bg-primary-500/10 p-4">
				<h4 class="mb-2 font-semibold text-primary-300">👥 Perhitungan Fee Amil</h4>
				<p>Fee Total = Absen (1-5) × Fee Dasar</p>
				<p class="mt-1 text-xs text-white/40">Contoh: Absen 5 × Rp.1.000.000 = Rp.5.000.000</p>
			</div>
		</div>
	</GlassCard>
</div>
