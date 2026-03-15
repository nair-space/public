<script lang="ts">
    import { fade, fly, slide } from "svelte/transition";
    import { cubicOut } from "svelte/easing";
    import { t } from "$lib/stores/translations.js";

    let equity = 10000;
    let riskPerTradePercent = 1;
    let stopLossPercent = 5; // Needed for position size
    let monthlyRiskPercent = 5;

    $: riskAmount = (equity * riskPerTradePercent) / 100;
    $: positionSize = riskAmount / (stopLossPercent / 100);
    $: monthlyRiskAmount = (equity * monthlyRiskPercent) / 100;

    // How many consecutive losses to hit monthly limit?
    // Using simple arithmetic for fixed dollar risk based on starting equity
    // (Conservative approach)
    $: tradesToMonthlyLimit = Math.floor(monthlyRiskAmount / riskAmount);

    // How many trades to blow up (reach ~0)?
    // Usually calculating to 50% drawdown is more realistic for "ruin", but user asked for "total equity exhausted"
    $: tradesToRuin = Math.floor(equity / riskAmount);

    // For geometric decay (if risk is dynamic), it never hits zero technically, but effectively does.
    // We'll stick to the user's likely mental model of "Fixed Risk based on initial stats" for the immediate display,
    // or clarify if it's dynamic. Let's assume Fixed % of CURRENT equity (Dynamic) is better for "Ruin" calc usually,
    // but Fixed $ of STARTING equity is what they probably calculate in their head.
    // Let's provide the Fixed % of Current calculation for "Ruin" as it's more accurate for trading.
    // Formula: N = log(End / Start) / log(1 - Risk)
    // Actually, simple is better for beginners. Let's show Fixed Risk based on current equity for single trade values,
    // but clarify the ruin part.
    // Let's stick to the arithmetic version for simplicity as requested by "beginner friendly" context usually.
    // But I will label it "Consecutive losses (Fixed Risk)".
</script>

<svelte:head>
    <title>{$t.moneyManagement.seoTitle}</title>
</svelte:head>

<div class="page-container">
    <div class="calculator-wrapper" in:fade={{ duration: 300 }}>
        <header class="header-section">
            <h1 class="title">{$t.moneyManagement.title}</h1>
            <p class="subtitle">
                {$t.moneyManagement.subtitle}
            </p>
            <div class="divider"></div>
        </header>

        <div class="calculator-grid">
            <!-- Inputs -->
            <div class="input-card">
                <h2>{$t.moneyManagement.accountSettings}</h2>

                <div class="input-group">
                    <label for="equity">{$t.moneyManagement.totalEquity}</label>
                    <div class="input-wrapper">
                        <span class="currency-symbol">$</span>
                        <input
                            type="number"
                            id="equity"
                            bind:value={equity}
                            min="0"
                            step="100"
                        />
                    </div>
                </div>

                <div class="input-group">
                    <label for="risk">{$t.moneyManagement.riskPerTrade}</label>
                    <div class="range-wrapper">
                        <input
                            type="range"
                            id="risk-slider"
                            bind:value={riskPerTradePercent}
                            min="0.1"
                            max="10"
                            step="0.1"
                        />
                        <div class="number-input">
                            <input
                                type="number"
                                id="risk"
                                bind:value={riskPerTradePercent}
                                min="0.1"
                                max="100"
                                step="0.1"
                            />
                            <span class="unit">%</span>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label for="stoploss"
                        >{$t.moneyManagement.stopLossSize}
                        <span
                            class="tooltip"
                            title={$t.moneyManagement.stopLossTooltip}>ℹ️</span
                        ></label
                    >
                    <p class="helper-text">
                        {$t.moneyManagement.helperPosSize}
                    </p>
                    <div class="range-wrapper">
                        <input
                            type="range"
                            id="sl-slider"
                            bind:value={stopLossPercent}
                            min="0.1"
                            max="100"
                            step="0.1"
                        />
                        <div class="number-input">
                            <input
                                type="number"
                                id="stoploss"
                                bind:value={stopLossPercent}
                                min="0.1"
                                max="100"
                                step="0.1"
                            />
                            <span class="unit">%</span>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label for="monthly-risk"
                        >{$t.moneyManagement.monthlyRiskLimit}</label
                    >
                    <div class="range-wrapper">
                        <input
                            type="range"
                            id="monthly-slider"
                            bind:value={monthlyRiskPercent}
                            min="1"
                            max="50"
                            step="0.5"
                        />
                        <div class="number-input">
                            <input
                                type="number"
                                id="monthly-risk"
                                bind:value={monthlyRiskPercent}
                                min="1"
                                max="100"
                                step="0.5"
                            />
                            <span class="unit">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="results-column">
                <div
                    class="result-card primary"
                    in:fly={{ y: 20, duration: 400, delay: 100 }}
                >
                    <div class="result-label">
                        {$t.moneyManagement.positionSize}
                    </div>
                    <div class="result-value large">
                        ${positionSize.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })}
                    </div>
                    <div class="result-subtext">
                        {$t.moneyManagement.posSizeSubtext
                            .replace("{sl}", stopLossPercent.toString())
                            .replace("{risk}", riskAmount.toFixed(2))}
                    </div>
                </div>

                <div class="mini-results-grid">
                    <div
                        class="result-card"
                        in:fly={{ y: 20, duration: 400, delay: 200 }}
                    >
                        <div class="result-icon">🛡️</div>
                        <div class="result-label">
                            {$t.moneyManagement.riskAmount}
                        </div>
                        <div class="result-value text-danger">
                            ${riskAmount.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            })}
                        </div>
                        <div class="result-subtext">
                            {$t.moneyManagement.riskSubtext}
                        </div>
                    </div>

                    <div
                        class="result-card"
                        in:fly={{ y: 20, duration: 400, delay: 300 }}
                    >
                        <div class="result-icon">📅</div>
                        <div class="result-label">
                            {$t.moneyManagement.monthlyLimit}
                        </div>
                        <div class="result-value text-warning">
                            ${monthlyRiskAmount.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            })}
                        </div>
                        <div class="result-subtext">
                            {$t.moneyManagement.monthlySubtext}
                        </div>
                    </div>
                </div>

                <div
                    class="stats-card"
                    in:fly={{ y: 20, duration: 400, delay: 400 }}
                >
                    <h3>{$t.moneyManagement.drawdownAnalysis}</h3>
                    <div class="stat-row">
                        <span>{$t.moneyManagement.tradesToMonthlyLimit}</span>
                        <span class="stat-value"
                            >{$t.moneyManagement.tradesUnit.replace(
                                "{count}",
                                tradesToMonthlyLimit.toString(),
                            )}</span
                        >
                    </div>
                    <div class="progress-bar-container">
                        <div
                            class="progress-bar"
                            style="width: 100%; background: rgba(255,255,255,0.1);"
                        >
                            <div
                                class="progress-fill warning"
                                style="width: {Math.min(
                                    (tradesToMonthlyLimit / 20) * 100,
                                    100,
                                )}%"
                            ></div>
                        </div>
                    </div>

                    <div class="stat-row mt-4">
                        <span>{$t.moneyManagement.tradesToExhaust}</span>
                        <span class="stat-value"
                            >{$t.moneyManagement.tradesUnit.replace(
                                "{count}",
                                tradesToRuin.toString(),
                            )}</span
                        >
                    </div>
                    <div class="progress-bar-container">
                        <div
                            class="progress-bar"
                            style="width: 100%; background: rgba(255,255,255,0.1);"
                        >
                            <div
                                class="progress-fill danger"
                                style="width: {Math.min(
                                    (tradesToRuin / 100) * 100,
                                    100,
                                )}%"
                            ></div>
                        </div>
                    </div>
                    <p class="disclaimer">
                        {$t.moneyManagement.disclaimer}
                    </p>
                </div>
            </div>
        </div>

        <div class="action-area">
            <a href="/" class="home-btn">← {$t.actions.backHome}</a>
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
        padding: 6rem 1rem 2rem;
        background: radial-gradient(
                circle at 50% 10%,
                rgba(0, 176, 155, 0.05) 0%,
                transparent 40%
            ),
            radial-gradient(
                circle at 50% 90%,
                rgba(150, 201, 61, 0.05) 0%,
                transparent 40%
            );
        color: white;
    }

    .calculator-wrapper {
        max-width: 1000px;
        margin: 0 auto;
    }

    .header-section {
        text-align: center;
        margin-bottom: 3rem;
    }

    .title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .subtitle {
        color: #94a3b8;
        font-size: 1.1rem;
    }

    .divider {
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #00b09b, #96c93d);
        margin: 1.5rem auto 0;
        border-radius: 2px;
    }

    .calculator-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    /* Input Card */
    .input-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 2rem;
        backdrop-filter: blur(10px);
    }

    .input-card h2 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        color: #e2e8f0;
    }

    .input-group {
        margin-bottom: 2rem;
    }

    .input-group:last-child {
        margin-bottom: 0;
    }

    .input-group label {
        display: block;
        margin-bottom: 0.8rem;
        color: #94a3b8;
        font-size: 0.95rem;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .helper-text {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: -0.5rem;
        margin-bottom: 0.8rem;
        font-style: italic;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        transition: all 0.2s;
    }

    .input-wrapper:focus-within {
        border-color: #00b09b;
        box-shadow: 0 0 0 2px rgba(0, 176, 155, 0.1);
    }

    .currency-symbol {
        padding-left: 1rem;
        color: #64748b;
        font-weight: 600;
    }

    input[type="number"] {
        flex: 1;
        background: transparent;
        border: none;
        color: white;
        padding: 1rem;
        font-size: 1.1rem;
        font-family: inherit;
        outline: none;
        width: 100%;
    }

    /* Range inputs */
    .range-wrapper {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    input[type="range"] {
        flex: 1;
        cursor: pointer;
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        outline: none;
        appearance: none;
    }

    input[type="range"]::-webkit-slider-thumb {
        appearance: none;
        width: 20px;
        height: 20px;
        background: #00b09b;
        border-radius: 50%;
        cursor: pointer;
        transition: transform 0.1s;
    }

    input[type="range"]::-webkit-slider-thumb:hover {
        transform: scale(1.1);
    }

    .number-input {
        display: flex;
        align-items: center;
        width: 100px;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .number-input input {
        padding: 0.5rem;
        font-size: 1rem;
        text-align: right;
    }

    .unit {
        padding-right: 0.8rem;
        color: #64748b;
        font-size: 0.9rem;
    }

    /* Results */
    .results-column {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .result-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 1.5rem;
        backdrop-filter: blur(10px);
    }

    .result-card.primary {
        background: linear-gradient(
            135deg,
            rgba(0, 176, 155, 0.1) 0%,
            rgba(150, 201, 61, 0.1) 100%
        );
        border: 1px solid rgba(0, 176, 155, 0.2);
        text-align: center;
        padding: 2.5rem 2rem;
    }

    .result-label {
        color: #94a3b8;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .result-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
    }

    .result-value.large {
        font-size: 3rem;
        margin: 0.5rem 0;
        background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .result-value.text-danger {
        color: #f5576c;
    }
    .result-value.text-warning {
        color: #fbbf24;
    }

    .result-subtext {
        color: #64748b;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .mini-results-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .stats-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 1.5rem 2rem;
    }

    .stats-card h3 {
        margin: 0 0 1.5rem 0;
        font-size: 1.1rem;
        color: #e2e8f0;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        color: #cbd5e1;
    }

    .stat-value {
        font-weight: 700;
        color: white;
    }

    .progress-bar-container {
        height: 6px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .progress-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .progress-fill.warning {
        background: #fbbf24;
    }
    .progress-fill.danger {
        background: #f5576c;
    }

    .mt-4 {
        margin-top: 1.5rem;
    }

    .disclaimer {
        font-size: 0.75rem;
        color: #475569;
        margin-top: 1rem;
        font-style: italic;
    }

    .action-area {
        margin-top: 3rem;
        text-align: center;
    }

    .home-btn {
        color: #94a3b8;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .home-btn:hover {
        color: white;
    }

    @media (max-width: 768px) {
        .calculator-grid {
            grid-template-columns: 1fr;
        }

        .result-value.large {
            font-size: 2.5rem;
        }
    }
</style>
