<script lang="ts">
    import { onMount } from "svelte";
    import { t } from "$lib/stores/translations.js";

    let isVisible = false;

    onMount(() => {
        isVisible = true;
    });

    $: mindsetPoints = $t.tradingPlan.mindset.points;
    $: affirmationPoints = $t.tradingPlan.affirmation.points;

    function handleDownload() {
        window.print();
    }
</script>

<svelte:head>
    <title>{$t.tradingPlan.title} | {$t.tradingPlan.subtitle}</title>
</svelte:head>

<div class="page-container">
    <div class="content-wrapper" class:visible={isVisible}>
        <header class="plan-header">
            <h1 class="main-title">{$t.tradingPlan.title}</h1>
            <h2 class="sub-title gradient-text">{$t.tradingPlan.subtitle}</h2>
            <div class="divider"></div>
        </header>

        <div class="grid-container">
            <!-- Strategy Section -->
            <section class="card strategy full-width-card">
                <h3>
                    <span class="icon">📊</span>
                    {$t.tradingPlan.marketSetup.title}
                </h3>

                <div class="info-group">
                    <h4>{$t.tradingPlan.marketSetup.marketTimeframe.title}</h4>
                    <p>
                        {@html $t.tradingPlan.marketSetup.marketTimeframe
                            .content}
                    </p>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.marketSetup.tradingSession.title}</h4>
                    <p>
                        {@html $t.tradingPlan.marketSetup.tradingSession
                            .content}
                    </p>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.marketSetup.entryIdeas.title}</h4>
                    <div class="concept-box">
                        <p>
                            {@html $t.tradingPlan.marketSetup.entryIdeas.long}
                        </p>
                        <p>
                            {@html $t.tradingPlan.marketSetup.entryIdeas.short}
                        </p>
                        <p>
                            {@html $t.tradingPlan.marketSetup.entryIdeas.logic}
                        </p>
                    </div>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.marketSetup.entryMethod.title}</h4>
                    <p>
                        {@html $t.tradingPlan.marketSetup.entryMethod.content}
                    </p>
                </div>
            </section>

            <!-- Execution Section -->
            <section class="card execution full-width-card">
                <h3>
                    <span class="icon">⚡</span>
                    {$t.tradingPlan.execution.title}
                </h3>

                <div class="info-group">
                    <h4>{$t.tradingPlan.execution.stopLoss.title}</h4>
                    <p>{@html $t.tradingPlan.execution.stopLoss.content}</p>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.execution.exitStrategy.title}</h4>
                    <p>{@html $t.tradingPlan.execution.exitStrategy.content}</p>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.execution.stopOutStrategy.title}</h4>
                    <p>
                        {@html $t.tradingPlan.execution.stopOutStrategy.content}
                    </p>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.execution.runningTrade.title}</h4>
                    <p>{@html $t.tradingPlan.execution.runningTrade.content}</p>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.execution.haltProtocol.title}</h4>
                    <p>{@html $t.tradingPlan.execution.haltProtocol.content}</p>
                </div>

                <div class="info-group">
                    <h4>{$t.tradingPlan.execution.indicators.title}</h4>
                    <p>{@html $t.tradingPlan.execution.indicators.content}</p>
                </div>
            </section>

            <!-- Money Management Section -->
            <section class="card risk full-width-card">
                <h3>
                    <span class="icon">💰</span>
                    {$t.tradingPlan.moneyManagement.title}
                </h3>

                <div class="risk-grid">
                    <div class="risk-item">
                        <h4>{$t.tradingPlan.moneyManagement.drawdown.title}</h4>
                        <p>
                            {@html $t.tradingPlan.moneyManagement.drawdown
                                .content}
                        </p>
                    </div>

                    <div class="risk-item">
                        <h4>{$t.tradingPlan.moneyManagement.onProfit.title}</h4>
                        <p>
                            {@html $t.tradingPlan.moneyManagement.onProfit
                                .content}
                        </p>
                    </div>

                    <div class="risk-item">
                        <h4>
                            {$t.tradingPlan.moneyManagement.riskReward.title}
                        </h4>
                        <p>
                            {@html $t.tradingPlan.moneyManagement.riskReward
                                .content}
                        </p>
                        <small
                            >{$t.tradingPlan.moneyManagement.riskReward
                                .note}</small
                        >
                    </div>

                    <div class="risk-item">
                        <h4>
                            {$t.tradingPlan.moneyManagement.riskAllocation
                                .title}
                        </h4>
                        <p>
                            {@html $t.tradingPlan.moneyManagement.riskAllocation
                                .content}
                        </p>
                    </div>

                    <div class="risk-item full-width">
                        <h4>
                            {$t.tradingPlan.moneyManagement.positionSizing
                                .title}
                        </h4>
                        <p>
                            {@html $t.tradingPlan.moneyManagement.positionSizing
                                .content}
                        </p>
                    </div>

                    <div class="risk-item full-width">
                        <h4>
                            {$t.tradingPlan.moneyManagement.riskExposure.title}
                        </h4>
                        <p>
                            {@html $t.tradingPlan.moneyManagement.riskExposure
                                .content}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Mindset Section -->
            <section class="card mindset full-width-card">
                <h3>
                    <span class="icon">🧠</span>
                    {$t.tradingPlan.mindset.title}
                </h3>
                <div class="list-grid">
                    {#each mindsetPoints as point}
                        <div class="list-item">
                            <span class="bullet">•</span>
                            <span>{point}</span>
                        </div>
                    {/each}
                </div>
            </section>

            <!-- Affirmation Section -->
            <section class="card affirmation full-width-card">
                <h3>
                    <span class="icon">🌟</span>
                    {$t.tradingPlan.affirmation.title}
                </h3>
                <div class="quote-box">
                    {#each affirmationPoints as point}
                        <p class="affirmation-text">"{point}"</p>
                    {/each}
                </div>
            </section>
        </div>

        <div class="action-area">
            <button class="btn-download" onclick={handleDownload}>
                <span class="icon">📥</span>
                {$t.actions.downloadPlan}
            </button>
            <a href="/" class="btn-back">{$t.actions.backHome}</a>
        </div>
    </div>
</div>

<style>
    :global(body) {
        background: #0f1419;
        font-family: "Inter", sans-serif;
    }

    .page-container {
        min-height: 100vh;
        padding: 6rem 2rem 4rem;
        color: #ffffff;
        background: radial-gradient(
                circle at 10% 20%,
                rgba(37, 99, 235, 0.05) 0%,
                transparent 40%
            ),
            radial-gradient(
                circle at 90% 80%,
                rgba(124, 58, 237, 0.05) 0%,
                transparent 40%
            );
    }

    .content-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease;
    }

    .content-wrapper.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .plan-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .main-title {
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 4px;
        color: #94a3b8;
        margin-bottom: 0.5rem;
    }

    .sub-title {
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, #60a5fa 0%, #c084fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .divider {
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #60a5fa, #c084fc);
        margin: 1.5rem auto 0;
        border-radius: 2px;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 2.5rem;
        backdrop-filter: blur(10px);
        transition:
            transform 0.3s ease,
            border-color 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        border-color: rgba(96, 165, 250, 0.3);
    }

    .full-width-card {
        grid-column: 1 / -1;
    }

    h3 {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-size: 1.5rem;
        margin: 0 0 2rem 0;
        color: #ffffff;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1rem;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group:last-child {
        margin-bottom: 0;
    }

    h4 {
        color: #94a3b8;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 0 0.5rem 0;
    }

    p {
        margin: 0;
        line-height: 1.6;
        color: #e2e8f0;
    }

    :global(strong) {
        color: #60a5fa;
    }

    /* Strategy Specifics */
    .concept-box {
        background: rgba(96, 165, 250, 0.1);
        padding: 1rem;
        border-radius: 12px;
        border-left: 3px solid #60a5fa;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    :global(.halt-warning) {
        color: #f87171;
        font-weight: bold;
    }

    /* Risk Grid */
    .risk-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .risk-item.full-width {
        grid-column: 1 / -1;
    }

    /* Mindset List */
    .list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
    }

    .list-item {
        display: flex;
        gap: 0.8rem;
        line-height: 1.5;
        color: #cbd5e1;
    }

    .bullet {
        color: #c084fc;
    }

    /* Affirmation */
    .quote-box {
        background: linear-gradient(
            135deg,
            rgba(96, 165, 250, 0.05),
            rgba(192, 132, 252, 0.05)
        );
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .affirmation-text {
        color: #e2e8f0;
        font-style: italic;
        margin-bottom: 1rem;
        padding-left: 1rem;
        border-left: 2px solid #60a5fa;
    }

    .affirmation-text:last-child {
        margin-bottom: 0;
    }

    /* Action Area */
    .action-area {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        margin-top: 4rem;
        padding-bottom: 4rem;
    }

    .btn-download {
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        transition:
            transform 0.2s,
            box-shadow 0.2s;
    }

    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    }

    .btn-back {
        color: #94a3b8;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-container {
            padding: 5rem 1rem 2rem;
        }

        .grid-container {
            grid-template-columns: 1fr;
        }

        .risk-grid {
            grid-template-columns: 1fr;
        }

        .action-area {
            flex-direction: column;
            gap: 1.5rem;
        }

        .btn-download {
            width: 100%;
            justify-content: center;
        }
    }

    /* Print Styles */
    @media print {
        :global(body) {
            background: white;
            color: black;
        }
        .page-container {
            background: none;
            padding: 0;
            color: black;
        }
        .content-wrapper {
            opacity: 1;
            transform: none;
        }
        .card {
            border: 1px solid #ccc;
            background: none;
            color: black;
            break-inside: avoid;
            margin-bottom: 2rem;
        }
        h1,
        h2,
        h3,
        h4,
        p,
        :global(strong),
        span,
        .bullet {
            color: black !important;
            background: none !important;
            -webkit-text-fill-color: black !important;
        }
        .action-area {
            display: none;
        }
    }
</style>
