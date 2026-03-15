<script>
    import { onMount } from "svelte";

    let isVisible = false;

    onMount(() => {
        isVisible = true;
    });

    const approaches = [
        {
            icon: "🔍",
            title: "Data-Driven Analysis",
            description:
                "We leverage cutting-edge analytics and real-time market data to identify opportunities and mitigate risks across all asset classes.",
        },
        {
            icon: "⚖️",
            title: "Balanced Portfolio Strategy",
            description:
                "Our diversification approach spreads investments across crypto, forex, and commodities to optimize returns while managing volatility.",
        },
        {
            icon: "🛡️",
            title: "Risk-First Methodology",
            description:
                "Every investment decision is evaluated through our comprehensive risk assessment framework to protect your capital.",
        },
        {
            icon: "📊",
            title: "Performance Tracking",
            description:
                "Transparent, real-time reporting keeps you informed about your portfolio performance and market movements.",
        },
    ];

    const markets = [
        {
            title: "Cryptocurrency Markets",
            icon: "₿",
            points: [
                "Bitcoin, Ethereum, and major altcoins",
                "DeFi protocol investments",
                "NFT and Web3 opportunities",
                "Staking and yield optimization",
                "24/7 market monitoring",
            ],
        },
        {
            title: "Foreign Exchange",
            icon: "💱",
            points: [
                "Major currency pairs (EUR/USD, GBP/USD, etc.)",
                "Emerging market currencies",
                "Cross-currency hedging strategies",
                "Macroeconomic analysis",
                "Central bank policy tracking",
            ],
        },
        {
            title: "Commodity Trading",
            icon: "🏆",
            points: [
                "Precious metals (Gold, Silver, Platinum)",
                "Energy markets (Oil, Natural Gas)",
                "Agricultural commodities",
                "Industrial metals",
                "Futures and options strategies",
            ],
        },
    ];

    const process = [
        {
            step: "01",
            title: "Initial Consultation",
            description:
                "We begin with a comprehensive discussion of your financial goals, risk tolerance, and investment timeline.",
        },
        {
            step: "02",
            title: "Portfolio Design",
            description:
                "Our team crafts a customized portfolio strategy tailored to your specific objectives and market outlook.",
        },
        {
            step: "03",
            title: "Implementation",
            description:
                "We execute your strategy with precision, leveraging our institutional-grade trading infrastructure.",
        },
        {
            step: "04",
            title: "Ongoing Management",
            description:
                "Continuous monitoring, rebalancing, and optimization ensure your portfolio stays aligned with your goals.",
        },
    ];
</script>

<svelte:head>
    <title>Learn More - Elite Portfolio Management</title>
    <meta
        name="description"
        content="Discover our comprehensive approach to portfolio management across cryptocurrency, forex, and commodity markets. Expert strategies backed by data-driven insights."
    />
</svelte:head>

<div class="learn-more-page">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content" class:visible={isVisible}>
                <h1 class="page-title">
                    <span class="title-accent">Our Approach to</span>
                    <span class="gradient-text">Portfolio Excellence</span>
                </h1>
                <p class="page-subtitle">
                    Combining decades of market expertise with cutting-edge
                    technology to deliver superior investment outcomes across
                    global markets.
                </p>
            </div>
        </div>
    </section>

    <!-- Approach Section -->
    <section class="approach-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Investment Philosophy</h2>
                <p class="section-subtitle">
                    Built on four core principles that guide every decision we
                    make
                </p>
            </div>

            <div class="approach-grid">
                {#each approaches as approach, i}
                    <div
                        class="approach-card"
                        style="animation-delay: {i * 0.1}s"
                    >
                        <div class="approach-icon">{approach.icon}</div>
                        <h3 class="approach-title">{approach.title}</h3>
                        <p class="approach-description">
                            {approach.description}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- Markets Section -->
    <section class="markets-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Markets We Cover</h2>
                <p class="section-subtitle">
                    Comprehensive coverage across the world's most dynamic asset
                    classes
                </p>
            </div>

            <div class="markets-grid">
                {#each markets as market, i}
                    <div
                        class="market-card"
                        style="animation-delay: {i * 0.15}s"
                    >
                        <div class="market-header">
                            <div class="market-icon">{market.icon}</div>
                            <h3 class="market-title">{market.title}</h3>
                        </div>
                        <ul class="market-points">
                            {#each market.points as point}
                                <li>{point}</li>
                            {/each}
                        </ul>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">How We Work</h2>
                <p class="section-subtitle">
                    A proven four-step process designed to maximize your
                    investment success
                </p>
            </div>

            <div class="process-timeline">
                {#each process as item, i}
                    <div
                        class="process-item"
                        style="animation-delay: {i * 0.2}s"
                    >
                        <div class="process-step">{item.step}</div>
                        <div class="process-content">
                            <h3 class="process-title">{item.title}</h3>
                            <p class="process-description">
                                {item.description}
                            </p>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- Stats Banner -->
    <section class="stats-banner">
        <div class="container">
            <div class="stats-content">
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Years of Excellence</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">$500M+</div>
                    <div class="stat-label">Assets Managed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Client Retention</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Market Coverage</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-description">
                    Schedule a consultation with our portfolio management team
                    to discuss your investment goals.
                </p>
                <div class="cta-buttons">
                    <a href="/#get-started" class="btn btn-primary">
                        Schedule Consultation
                        <span class="btn-arrow">→</span>
                    </a>
                    <a href="/" class="btn btn-outline"> Back to Home </a>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .learn-more-page {
        background: #0a0a0f;
        color: #ffffff;
        min-height: 100vh;
        padding-top: 5rem;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* Hero Section */
    .hero {
        padding: 6rem 0 4rem;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: "";
        position: absolute;
        top: -50%;
        left: 50%;
        transform: translateX(-50%);
        width: 800px;
        height: 800px;
        background: radial-gradient(
            circle,
            rgba(102, 126, 234, 0.15) 0%,
            transparent 70%
        );
        pointer-events: none;
    }

    .hero-content {
        text-align: center;
        position: relative;
        z-index: 1;
        opacity: 0;
        transform: translateY(30px);
        transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hero-content.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .page-title {
        font-size: clamp(2.5rem, 6vw, 4.5rem);
        font-weight: 800;
        line-height: 1.2;
        margin: 0 0 1.5rem 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .title-accent {
        font-size: clamp(1.5rem, 3vw, 2.5rem);
        font-weight: 600;
        color: #b4b4c8;
    }

    .gradient-text {
        background: linear-gradient(
            135deg,
            #667eea 0%,
            #764ba2 50%,
            #f093fb 100%
        );
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        font-size: clamp(1.1rem, 2vw, 1.3rem);
        color: #b4b4c8;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* Section Styles */
    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        margin: 0 0 1rem 0;
        background: linear-gradient(135deg, #ffffff 0%, #b4b4c8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-subtitle {
        font-size: 1.2rem;
        color: #b4b4c8;
        max-width: 700px;
        margin: 0 auto;
    }

    /* Approach Section */
    .approach-section {
        padding: 6rem 0;
        background: rgba(255, 255, 255, 0.02);
    }

    .approach-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
    }

    .approach-card {
        padding: 2.5rem 2rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        opacity: 0;
        animation: fadeInUp 0.8s ease forwards;
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

    .approach-card:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-5px);
        border-color: rgba(102, 126, 234, 0.3);
    }

    .approach-icon {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
    }

    .approach-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
    }

    .approach-description {
        color: #b4b4c8;
        line-height: 1.7;
        margin: 0;
    }

    /* Markets Section */
    .markets-section {
        padding: 6rem 0;
    }

    .markets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2.5rem;
    }

    .market-card {
        padding: 3rem 2.5rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        animation: fadeInUp 0.8s ease forwards;
        position: relative;
        overflow: hidden;
    }

    .market-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            135deg,
            rgba(102, 126, 234, 0.1) 0%,
            rgba(118, 75, 162, 0.1) 100%
        );
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .market-card:hover {
        transform: translateY(-10px);
        border-color: rgba(102, 126, 234, 0.5);
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    }

    .market-card:hover::before {
        opacity: 1;
    }

    .market-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .market-icon {
        font-size: 3rem;
    }

    .market-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
    }

    .market-points {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .market-points li {
        padding: 0.8rem 0;
        padding-left: 2rem;
        color: #b4b4c8;
        position: relative;
        line-height: 1.6;
    }

    .market-points li::before {
        content: "✓";
        position: absolute;
        left: 0;
        color: #667eea;
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* Process Section */
    .process-section {
        padding: 6rem 0;
        background: rgba(255, 255, 255, 0.02);
    }

    .process-timeline {
        max-width: 900px;
        margin: 0 auto;
    }

    .process-item {
        display: flex;
        gap: 2rem;
        margin-bottom: 3rem;
        opacity: 0;
        animation: fadeInUp 0.8s ease forwards;
        position: relative;
    }

    .process-item:not(:last-child)::after {
        content: "";
        position: absolute;
        left: 2.5rem;
        top: 5rem;
        width: 2px;
        height: calc(100% + 1rem);
        background: linear-gradient(
            to bottom,
            rgba(102, 126, 234, 0.5),
            transparent
        );
    }

    .process-step {
        flex-shrink: 0;
        width: 5rem;
        height: 5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .process-content {
        flex: 1;
        padding-top: 0.5rem;
    }

    .process-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
    }

    .process-description {
        color: #b4b4c8;
        line-height: 1.7;
        margin: 0;
        font-size: 1.1rem;
    }

    /* Stats Banner */
    .stats-banner {
        padding: 5rem 0;
        background: linear-gradient(
            135deg,
            rgba(102, 126, 234, 0.15) 0%,
            rgba(118, 75, 162, 0.15) 100%
        );
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stats-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 3rem;
        text-align: center;
    }

    .stat-item {
        padding: 1rem;
    }

    .stat-number {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1.1rem;
        color: #b4b4c8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* CTA Section */
    .cta-section {
        padding: 6rem 0;
    }

    .cta-content {
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
    }

    .cta-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        margin: 0 0 1.5rem 0;
    }

    .cta-description {
        font-size: 1.2rem;
        color: #b4b4c8;
        margin: 0 0 2.5rem 0;
        line-height: 1.7;
    }

    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 1rem 2.5rem;
        font-size: 1.1rem;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
    }

    .btn-primary .btn-arrow {
        transition: transform 0.3s ease;
    }

    .btn-primary:hover .btn-arrow {
        transform: translateX(5px);
    }

    .btn-outline {
        background: transparent;
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .learn-more-page {
            padding-top: 4rem;
        }

        .container {
            padding: 0 1.5rem;
        }

        .hero {
            padding: 4rem 0 3rem;
        }

        .approach-grid,
        .markets-grid {
            grid-template-columns: 1fr;
        }

        .process-item {
            flex-direction: column;
            gap: 1rem;
        }

        .process-item:not(:last-child)::after {
            left: 2.5rem;
            top: 6rem;
        }

        .stats-content {
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .cta-buttons {
            flex-direction: column;
            width: 100%;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
