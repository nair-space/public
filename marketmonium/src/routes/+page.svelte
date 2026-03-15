<script lang="ts">
	import { onMount } from "svelte";
	import { t } from "$lib/stores/translations.js";

	let scrollY = 0;
	let isVisible = false;

	onMount(() => {
		isVisible = true;

		const handleScroll = () => {
			scrollY = window.scrollY;
		};

		window.addEventListener("scroll", handleScroll);
		return () => window.removeEventListener("scroll", handleScroll);
	});

	$: features = [
		{
			icon: "📋",
			title: $t.landing.cardPlanTitle,
			description: $t.landing.cardPlanDesc,
			link: "/trading-plan",
			gradient: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
		},
		{
			icon: "📊",
			title: $t.landing.cardJournalTitle,
			description: $t.landing.cardJournalDesc,
			link: "/trading-journal",
			gradient: "linear-gradient(135deg, #f093fb 0%, #f5576c 100%)",
		},
		{
			icon: "🎯",
			title: $t.landing.cardQuizTitle,
			description: $t.landing.cardQuizDesc,
			link: "/quiz",
			gradient: "linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)",
		},
	];

	$: benefits = $t.landing.benefits;

	$: calculators = [
		{
			icon: "🧮",
			title: $t.landing.cardMmTitle,
			description: $t.landing.cardMmDesc,
			link: "/calculators/money-management",
			gradient: "linear-gradient(135deg, #00b09b 0%, #96c93d 100%)",
		},
		{
			icon: "🎲",
			title: $t.landing.cardCoinTitle,
			description: $t.landing.cardCoinDesc,
			link: "/calculators/coin-flip",
			gradient: "linear-gradient(135deg, #FF512F 0%, #DD2476 100%)",
		},
	];
</script>

<svelte:head>
	<title>{$t.landing.seoTitle}</title>
	<meta name="description" content={$t.landing.seoDesc} />
	<meta
		name="keywords"
		content="trading education, beginner trading, trading plan, trading journal, money management, trading quiz"
	/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</svelte:head>

<div class="landing-page">
	<!-- 
	
	<section class="hero" id="hero">
		<div class="hero-background">
			<div class="gradient-orb orb-1"></div>
			<div class="gradient-orb orb-2"></div>
			<div class="gradient-orb orb-3"></div>
		</div>


		<div class="hero-content">
			<h1 class="hero-title">
				<span class="title-line">Master Trading</span>
				<span class="title-line gradient-text">From Zero to Hero</span>
			</h1>
			<p class="hero-subtitle">
				Your complete baseline guide to trading. Learn the fundamentals,
				build discipline, and develop the skills every successful trader
				needs.
			</p>
			<div class="cta-buttons">
				<a href="#features" class="btn btn-primary">
					Start Learning
					<span class="btn-arrow">→</span>
				</a>
				<a href="#benefits" class="btn btn-secondary"
					>Why Learn Trading?</a
				>
			</div>
		</div>

		

		<div class="scroll-indicator">
			<div class="scroll-line"></div>
		</div>
	</section>

	-->

	<!-- Features Section -->
	<section class="features-section" id="features">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">{$t.landing.featuresTitle}</h2>
				<p class="section-subtitle">
					{$t.landing.featuresSubtitle}
				</p>
			</div>

			<div class="features-grid">
				{#each features as feature, i}
					<div
						class="feature-card"
						style="animation-delay: {i * 0.15}s"
					>
						<a href={feature.link} class="icon-link">
							<div
								class="feature-icon-wrapper"
								style="background: {feature.gradient}"
							>
								<div class="feature-icon">{feature.icon}</div>
							</div>
						</a>
						<h3 class="feature-title">
							<a href={feature.link} class="title-link"
								>{feature.title}</a
							>
						</h3>
						<p class="feature-description">{feature.description}</p>
						<a href={feature.link} class="feature-link">
							{$t.actions.explore}
							<span class="link-arrow">→</span>
						</a>
					</div>
				{/each}
			</div>
		</div>
	</section>

	<!-- Calculators Section -->
	<section class="calculators-section" id="calculators">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">{$t.landing.toolsTitle}</h2>
				<p class="section-subtitle">
					{$t.landing.toolsSubtitle}
				</p>
			</div>

			<div class="features-grid">
				{#each calculators as calculator, i}
					<div
						class="feature-card"
						style="animation-delay: {i * 0.15}s"
					>
						<a href={calculator.link} class="icon-link">
							<div
								class="feature-icon-wrapper"
								style="background: {calculator.gradient}"
							>
								<div class="feature-icon">
									{calculator.icon}
								</div>
							</div>
						</a>
						<h3 class="feature-title">
							<a href={calculator.link} class="title-link"
								>{calculator.title}</a
							>
						</h3>
						<p class="feature-description">
							{calculator.description}
						</p>
						<a href={calculator.link} class="feature-link">
							{$t.actions.useTool}
							<span class="link-arrow">→</span>
						</a>
					</div>
				{/each}
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<div class="footer-content">
				<div class="footer-brand">
					<h3 class="brand-name">{$t.nav.brand}</h3>
					<p class="brand-tagline">
						{$t.footer.tagline}
					</p>
				</div>
				<div class="footer-links">
					<div class="footer-column">
						<h4>{$t.footer.modules}</h4>
						<a href="/trading-plan">{$t.nav.tradingPlan}</a>
						<a href="/trading-journal">{$t.nav.tradingJournal}</a>
						<a href="/quiz">{$t.nav.quiz}</a>
					</div>
				</div>
			</div>
			<div class="footer-bottom">
				<p>
					&copy; 2026 {$t.footer.rights}
				</p>
			</div>
		</div>
	</footer>
</div>

<style>
	:global(body) {
		margin: 0;
		padding: 0;
		overflow-x: hidden;
		font-family:
			"Inter",
			-apple-system,
			BlinkMacSystemFont,
			"Segoe UI",
			Roboto,
			sans-serif;
	}

	.landing-page {
		background: #0f1419;
		color: #ffffff;
		min-height: 100vh;
	}

	.container {
		max-width: 1200px;
		margin: 0 auto;
		padding: 0 2rem;
	}

	/* Features Section */
	.features-section {
		padding: 8rem 0;
		background: linear-gradient(180deg, #0f1419 0%, #1a1f2e 100%);
	}

	.calculators-section {
		padding: 8rem 0;
		background: linear-gradient(180deg, #1a1f2e 0%, #0f1419 100%);
	}

	.section-header {
		text-align: center;
		margin-bottom: 4rem;
	}

	.section-title {
		font-size: clamp(2.5rem, 5vw, 4rem);
		font-weight: 800;
		margin: 0 0 1rem 0;
		background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
		letter-spacing: -0.02em;
	}

	.section-subtitle {
		font-size: 1.2rem;
		color: #94a3b8;
		max-width: 600px;
		margin: 0 auto;
		font-weight: 400;
	}

	.features-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 2rem;
	}

	.feature-card {
		padding: 3rem 2rem;
		background: rgba(255, 255, 255, 0.03);
		border-radius: 24px;
		border: 1px solid rgba(255, 255, 255, 0.08);
		backdrop-filter: blur(10px);
		transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
		opacity: 0;
		animation: fadeInUp 0.8s ease forwards;
		position: relative;
		overflow: hidden;
		text-align: center;
	}

	.feature-card::before {
		content: "";
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: linear-gradient(
			135deg,
			rgba(59, 130, 246, 0.05) 0%,
			rgba(139, 92, 246, 0.05) 100%
		);
		opacity: 0;
		transition: opacity 0.4s ease;
	}

	.feature-card:hover {
		transform: translateY(-10px);
		border-color: rgba(59, 130, 246, 0.3);
		box-shadow: 0 20px 60px rgba(59, 130, 246, 0.2);
	}

	.feature-card:hover::before {
		opacity: 1;
	}

	.icon-link {
		text-decoration: none;
		display: block;
		width: fit-content;
		margin: 0 auto;
	}

	.title-link {
		text-decoration: none;
		color: inherit;
		transition: color 0.3s ease;
	}

	.title-link:hover {
		color: #60a5fa;
	}

	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(30px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.feature-icon-wrapper {
		width: 80px;
		height: 80px;
		margin: 0 auto 1.5rem;
		border-radius: 20px;
		display: flex;
		align-items: center;
		justify-content: center;
		position: relative;
		z-index: 1;
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
	}

	.feature-icon {
		font-size: 3rem;
		position: relative;
		z-index: 1;
	}

	.feature-title {
		font-size: 1.8rem;
		font-weight: 700;
		margin: 0 0 1rem 0;
		position: relative;
		z-index: 1;
		color: #ffffff;
	}

	.feature-description {
		color: #94a3b8;
		line-height: 1.7;
		margin: 0 0 2rem 0;
		position: relative;
		z-index: 1;
		font-size: 1.05rem;
	}

	.feature-link {
		background: transparent;
		color: #3b82f6;
		border: none;
		font-size: 1.1rem;
		font-weight: 600;
		cursor: pointer;
		display: inline-flex;
		align-items: center;
		gap: 0.5rem;
		padding: 0;
		position: relative;
		z-index: 1;
		transition: all 0.3s ease;
		text-decoration: none;
	}

	.feature-link:hover {
		gap: 1rem;
		color: #60a5fa;
	}

	.link-arrow {
		transition: transform 0.3s ease;
	}

	.feature-link:hover .link-arrow {
		transform: translateX(5px);
	}

	/* Footer */
	.footer {
		background: rgba(0, 0, 0, 0.4);
		padding: 4rem 0 2rem;
		border-top: 1px solid rgba(255, 255, 255, 0.08);
	}

	.footer-content {
		display: grid;
		grid-template-columns: 1fr 2fr;
		gap: 4rem;
		margin-bottom: 3rem;
	}

	.brand-name {
		font-size: 1.8rem;
		font-weight: 800;
		margin: 0 0 0.5rem 0;
		background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
	}

	.brand-tagline {
		color: #94a3b8;
		margin: 0;
		font-size: 1.05rem;
	}

	.footer-links {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
		gap: 2rem;
	}

	.footer-column h4 {
		font-size: 1.1rem;
		font-weight: 700;
		margin: 0 0 1rem 0;
		color: #ffffff;
	}

	.footer-column a {
		display: block;
		color: #94a3b8;
		text-decoration: none;
		margin-bottom: 0.7rem;
		transition: color 0.3s ease;
		font-size: 1rem;
	}

	.footer-column a:hover {
		color: #3b82f6;
	}

	.footer-bottom {
		text-align: center;
		padding-top: 2rem;
		border-top: 1px solid rgba(255, 255, 255, 0.08);
		color: #94a3b8;
		font-size: 0.95rem;
	}

	/* Responsive Design */
	@media (max-width: 768px) {
		.container {
			padding: 0 1.5rem;
		}

		.features-grid {
			grid-template-columns: 1fr;
			gap: 1.5rem;
		}

		.footer-content {
			grid-template-columns: 1fr;
			gap: 2rem;
		}

		.features-section {
			padding: 5rem 0;
		}
	}

	@media (max-width: 480px) {
		.feature-card {
			padding: 2rem 1.5rem;
		}

		.feature-icon-wrapper {
			width: 70px;
			height: 70px;
		}

		.feature-icon {
			font-size: 2.5rem;
		}
	}
</style>
