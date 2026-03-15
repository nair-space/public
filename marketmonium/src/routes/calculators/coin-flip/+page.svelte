<script lang="ts">
    import { fade, fly, scale, slide } from "svelte/transition";
    import { cubicOut, elasticOut } from "svelte/easing";
    import { onMount } from "svelte";
    import Chart from "chart.js/auto";
    import { t } from "$lib/stores/translations.js";

    // Game State
    let totalHeads = 0;
    let totalTails = 0;
    let totalMoney = 0;
    let flipHistory: {
        result: "HEADS" | "TAILS";
        change: number;
        id: number;
    }[] = [];

    let chartCanvas: HTMLCanvasElement;
    let chartInstance: Chart;

    // Settings
    type GameMode = {
        value: 1 | 2 | 3;
        label: string;
        reward: number;
        baseChance: number;
        color: string;
    };
    let selectedMode: 1 | 2 | 3 = 1;
    let statisticalEdge = 0;
    let isFlipping = false;
    let lastResult: "HEADS" | "TAILS" | null = null;
    let lastChange = 0;

    const modes: GameMode[] = [
        {
            value: 1,
            label: "1:1 Reward",
            reward: 1,
            baseChance: 50,
            color: "#43e97b",
        },
        {
            value: 2,
            label: "2:1 Reward",
            reward: 2,
            baseChance: 33,
            color: "#f093fb",
        },
        {
            value: 3,
            label: "3:1 Reward",
            reward: 3,
            baseChance: 25,
            color: "#667eea",
        },
    ];

    $: currentMode = modes.find((m) => m.value === selectedMode) || modes[0];
    $: winChance = currentMode.baseChance + statisticalEdge;

    function flipCoin() {
        if (isFlipping) return;
        isFlipping = true;
        lastResult = null; // Reset for animation

        // Animation duration
        setTimeout(() => {
            const roll = Math.floor(Math.random() * 100) + 1; // 1 to 100
            const threshold = winChance;

            if (roll <= threshold) {
                // Win / HEADS
                handleResult("HEADS");
            } else {
                // Loss / TAILS
                handleResult("TAILS");
            }

            isFlipping = false;
        }, 1000); // 1 second flip animation
    }

    function handleResult(result: "HEADS" | "TAILS") {
        lastResult = result;

        if (result === "HEADS") {
            totalHeads++;
            lastChange = currentMode.reward;
            totalMoney += currentMode.reward;
        } else {
            totalTails++;
            lastChange = -1;
            totalMoney -= 1;
        }

        // Add to history (keep last 10)
        flipHistory = [
            { result, change: lastChange, id: Date.now() },
            ...flipHistory.slice(0, 9),
        ];

        updateChart(totalMoney);
    }

    function resetStats() {
        totalHeads = 0;
        totalTails = 0;
        totalMoney = 0;
        flipHistory = [];
        lastResult = null;

        if (chartInstance) {
            chartInstance.data.labels = ["Start"];
            chartInstance.data.datasets[0].data = [0];
            chartInstance.update();
        }
    }

    onMount(() => {
        if (chartCanvas) {
            const ctx = chartCanvas.getContext("2d");
            if (ctx) {
                chartInstance = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: ["Start"],
                        datasets: [
                            {
                                label: $t.coinFlip.results.totalPnL,
                                data: [0],
                                borderColor: "#667eea",
                                backgroundColor: "rgba(102, 126, 234, 0.1)",
                                tension: 0.3,
                                fill: true,
                                pointRadius: 4,
                                pointBackgroundColor: "#667eea",
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: "index",
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const val = context.parsed.y;
                                        if (val === null) return "";
                                        return `PnL: ${val > 0 ? "+" : ""}${val}`;
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                display: false, // Hide X axis labels for cleaner look
                                grid: {
                                    display: false,
                                },
                            },
                            y: {
                                grid: {
                                    color: "rgba(255, 255, 255, 0.1)",
                                },
                                ticks: {
                                    color: "#94a3b8",
                                    callback: function (value) {
                                        return (
                                            (Number(value) > 0 ? "+" : "") +
                                            value
                                        );
                                    },
                                },
                            },
                        },
                    },
                });
            }
        }
    });

    // Update dataset label when language changes
    $: if (chartInstance) {
        chartInstance.data.datasets[0].label = $t.coinFlip.results.totalPnL;
        chartInstance.update();
    }

    function updateChart(newTotal: number) {
        if (!chartInstance) return;
        const flipCount = totalHeads + totalTails;
        chartInstance.data.labels?.push(`Flip ${flipCount}`);
        chartInstance.data.datasets[0].data.push(newTotal);

        // Optional: Limit chart history if it gets too long
        if (
            chartInstance.data.labels &&
            chartInstance.data.labels.length > 50
        ) {
            chartInstance.data.labels.shift();
            chartInstance.data.datasets[0].data.shift();
        }

        chartInstance.update();
    }
</script>

<svelte:head>
    <title>{$t.coinFlip.title} | {$t.nav.brand}</title>
</svelte:head>

<div class="page-container">
    <div class="simulator-wrapper">
        <header class="header-section" in:fade={{ duration: 300 }}>
            <h1 class="title">{$t.coinFlip.title}</h1>
            <p class="subtitle">
                {$t.coinFlip.subtitle}
            </p>
            <div class="divider"></div>
        </header>

        <div class="main-grid">
            <!-- Left Panel: Controls -->
            <div
                class="panel controls-panel"
                in:fly={{ x: -20, duration: 400, delay: 100 }}
            >
                <h2>{$t.coinFlip.settingsTitle}</h2>

                <div class="control-group">
                    <span class="group-label">{$t.coinFlip.rewardScenario}</span
                    >
                    <div class="mode-selector">
                        {#each modes as mode}
                            <button
                                class="mode-btn"
                                class:active={selectedMode === mode.value}
                                onclick={() => (selectedMode = mode.value)}
                                style="--active-color: {mode.color}"
                            >
                                <span class="mode-reward">+{mode.reward}</span>
                                <span class="mode-chance"
                                    >{mode.baseChance}% {$t.coinFlip.stats
                                        .base}</span
                                >
                            </button>
                        {/each}
                    </div>
                </div>

                <div class="control-group">
                    <label class="group-label" for="stat-edge">
                        {$t.coinFlip.statEdge}
                        <span class="value-badge">+{statisticalEdge}%</span>
                    </label>
                    <p class="helper-text">
                        {$t.coinFlip.helperText}
                    </p>
                    <div class="range-wrapper">
                        <input
                            id="stat-edge"
                            type="range"
                            bind:value={statisticalEdge}
                            min="0"
                            max="50"
                            step="1"
                        />
                    </div>
                    <div class="stats-summary">
                        <div class="stat-row">
                            <span>{$t.coinFlip.stats.winProb}</span>
                            <span class="highlight">{winChance}%</span>
                        </div>
                        <div class="stat-row">
                            <span>{$t.coinFlip.stats.rewardWin}</span>
                            <span class="highlight success"
                                >+{currentMode.reward}</span
                            >
                        </div>
                        <div class="stat-row">
                            <span>{$t.coinFlip.stats.costLoss}</span>
                            <span class="highlight danger">-1</span>
                        </div>
                        <div class="stat-row">
                            <span>{$t.coinFlip.stats.ev}</span>
                            <span
                                class:positive-ev={(winChance / 100) *
                                    currentMode.reward -
                                    ((100 - winChance) / 100) * 1 >
                                    0}
                            >
                                {(
                                    (winChance / 100) * currentMode.reward -
                                    ((100 - winChance) / 100) * 1
                                ).toFixed(2)}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <button class="reset-btn" onclick={resetStats}
                        >{$t.actions.resetStats}</button
                    >
                    <a href="/" class="home-btn">{$t.actions.exitSimulator}</a>
                </div>
            </div>

            <!-- Center: The Coin -->
            <div class="coin-area">
                <div class="coin-container" class:flipping={isFlipping}>
                    <div
                        class="coin"
                        style="transform: rotateY({lastResult === 'TAILS'
                            ? 180
                            : 0}deg)"
                    >
                        <div class="side heads">
                            <span class="coin-icon">H</span>
                        </div>
                        <div class="side tails">
                            <span class="coin-icon">T</span>
                        </div>
                    </div>
                </div>

                <!-- Result Display -->
                <div class="result-display">
                    {#if lastResult}
                        <div
                            class="result-pop"
                            in:scale={{ duration: 400, easing: elasticOut }}
                        >
                            <span
                                class="result-text {lastResult === 'HEADS'
                                    ? 'success'
                                    : 'danger'}"
                            >
                                {lastResult}
                            </span>
                            <span class="result-amount">
                                {lastChange > 0 ? "+" : ""}{lastChange}
                            </span>
                        </div>
                    {:else if isFlipping}
                        <span class="status-text">{$t.coinFlip.flipping}</span>
                    {:else}
                        <span class="status-text placeholder"
                            >{$t.coinFlip.pressFlip}</span
                        >
                    {/if}
                </div>

                <button
                    class="flip-btn"
                    onclick={flipCoin}
                    disabled={isFlipping}
                >
                    {isFlipping ? "..." : $t.actions.flipCoin}
                </button>

                <!-- Chart Area -->
                <div class="chart-container">
                    <canvas bind:this={chartCanvas}></canvas>
                </div>
            </div>

            <!-- Right Panel: Results -->
            <div
                class="panel results-panel"
                in:fly={{ x: 20, duration: 400, delay: 200 }}
            >
                <h2>{$t.coinFlip.results.title}</h2>

                <div class="total-money">
                    <span class="label">{$t.coinFlip.results.totalPnL}</span>
                    <span
                        class="amount"
                        class:positive={totalMoney > 0}
                        class:negative={totalMoney < 0}
                    >
                        {totalMoney > 0 ? "+" : ""}{totalMoney}
                    </span>
                    <span class="currency">{$t.coinFlip.results.points}</span>
                </div>

                <div class="counters">
                    <div class="counter-box">
                        <span class="count-label"
                            >{$t.coinFlip.results.heads}</span
                        >
                        <span class="count-value success">{totalHeads}</span>
                    </div>
                    <div class="counter-box">
                        <span class="count-label"
                            >{$t.coinFlip.results.tails}</span
                        >
                        <span class="count-value danger">{totalTails}</span>
                    </div>
                </div>

                <div class="history-list">
                    <h3>{$t.coinFlip.results.recentFlips}</h3>
                    <div class="list-wrapper">
                        {#each flipHistory as item (item.id)}
                            <div
                                class="history-item"
                                in:slide={{ duration: 300 }}
                                class:win={item.change > 0}
                            >
                                <span class="item-result"
                                    >{item.result === "HEADS" ? "H" : "T"}</span
                                >
                                <span class="item-change"
                                    >{item.change > 0
                                        ? "+"
                                        : ""}{item.change}</span
                                >
                            </div>
                        {/each}
                        {#if flipHistory.length === 0}
                            <div class="empty-history">
                                {$t.coinFlip.results.noFlips}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
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
                rgba(255, 81, 47, 0.05) 0%,
                transparent 40%
            ),
            radial-gradient(
                circle at 50% 90%,
                rgba(221, 36, 118, 0.05) 0%,
                transparent 40%
            );
        color: white;
    }

    .simulator-wrapper {
        max-width: 1200px;
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
        background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%);
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
        background: linear-gradient(90deg, #ff512f, #dd2476);
        margin: 1.5rem auto 0;
        border-radius: 2px;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 350px 1fr 300px;
        gap: 2rem;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .main-grid {
            grid-template-columns: 1fr;
            max-width: 600px;
            margin: 0 auto;
        }

        .coin-area {
            order: -1; /* Coin on top on mobile */
            margin-bottom: 2rem;
        }
    }

    .panel {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 1.5rem;
        backdrop-filter: blur(10px);
    }

    .panel h2 {
        font-size: 1.2rem;
        color: #e2e8f0;
        margin: 0 0 1.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1rem;
    }

    .group-label {
        display: block;
        color: #94a3b8;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.8rem;
        display: flex;
        justify-content: space-between;
    }

    .control-group {
        margin-bottom: 2rem;
    }

    .mode-selector {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .mode-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem;
        border-radius: 12px;
        color: #e2e8f0;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .mode-btn:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .mode-btn.active {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--active-color);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
    }

    .mode-reward {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .mode-chance {
        font-size: 0.9rem;
        opacity: 0.7;
    }

    .value-badge {
        color: #43e97b;
        background: rgba(67, 233, 123, 0.1);
        padding: 0.1rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
    }

    .helper-text {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: -0.5rem;
        margin-bottom: 0.8rem;
    }

    /* Slider */
    input[type="range"] {
        width: 100%;
        cursor: pointer;
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        appearance: none;
        outline: none;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        background: #ff512f;
        border-radius: 50%;
        cursor: pointer;
        transition: transform 0.1s;
    }

    .stats-summary {
        margin-top: 1.5rem;
        background: rgba(0, 0, 0, 0.2);
        padding: 1rem;
        border-radius: 12px;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: #cbd5e1;
    }

    .stat-row:last-child {
        margin-bottom: 0;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .highlight {
        font-weight: 700;
        color: white;
    }
    .highlight.success {
        color: #43e97b;
    }
    .highlight.danger {
        color: #f5576c;
    }
    .positive-ev {
        color: #43e97b;
    }

    /* Coin Area */
    .coin-area {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 400px;
    }

    .coin-container {
        width: 150px;
        height: 150px;
        margin-bottom: 3rem;
        perspective: 1000px;
    }

    .coin {
        width: 100%;
        height: 100%;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .coin-container.flipping .coin {
        animation: flip-animation 0.5s infinite linear;
    }

    /* Show Head or Tail based on last result (static state) */
    /* This is a bit tricky with pure CSS state usually, but let's assume default is Heads, 
       and if result is Tails we rotate. But for a simulator, randomized spinning is key. 
       We will reset rotation via JS or just animate continuously during flip. */

    @keyframes flip-animation {
        0% {
            transform: rotateY(0);
        }
        100% {
            transform: rotateY(720deg);
        }
    }

    .side {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        backface-visibility: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 20px rgba(255, 81, 47, 0.5);
    }

    .side.heads {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        box-shadow: 0 0 20px rgba(67, 233, 123, 0.6);
        transform: rotateY(0deg);
    }

    .side.tails {
        background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%);
        transform: rotateY(180deg);
        border-color: #4a5568;
        box-shadow: 0 0 20px rgba(255, 81, 47, 0.5);
    }

    .coin-icon {
        font-size: 4rem;
        font-weight: 800;
        color: white;
    }

    .flip-btn {
        background: white;
        color: #ff512f;
        border: none;
        padding: 1rem 3rem;
        font-size: 1.2rem;
        font-weight: 800;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .flip-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(255, 81, 47, 0.3);
    }

    .flip-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .result-display {
        height: 80px;
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .result-pop {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .result-text {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.2rem;
        text-transform: uppercase;
    }
    .result-text.success {
        color: #43e97b;
        text-shadow: 0 0 10px rgba(67, 233, 123, 0.3);
    }
    .result-text.danger {
        color: #f5576c;
        text-shadow: 0 0 10px rgba(245, 87, 108, 0.3);
    }

    .result-amount {
        font-size: 1.2rem;
        font-weight: 600;
        color: #94a3b8;
    }
    .status-text {
        font-size: 1.2rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Results Panel */
    .total-money {
        text-align: center;
        background: rgba(0, 0, 0, 0.2);
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
    }

    .total-money .label {
        display: block;
        color: #94a3b8;
        font-size: 0.9rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .total-money .amount {
        display: block;
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
    }

    .total-money .amount.positive {
        color: #43e97b;
    }
    .total-money .amount.negative {
        color: #f5576c;
    }
    .total-money .currency {
        font-size: 0.9rem;
        color: #64748b;
    }

    .counters {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .counter-box {
        text-align: center;
        background: rgba(255, 255, 255, 0.05);
        padding: 1rem;
        border-radius: 12px;
    }

    .count-label {
        display: block;
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 0.2rem;
    }
    .count-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .count-value.success {
        color: #43e97b;
    }
    .count-value.danger {
        color: #f5576c;
    }

    .history-list h3 {
        font-size: 1rem;
        color: #94a3b8;
        margin-bottom: 1rem;
    }

    .list-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .history-item {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 8px;
        font-size: 0.9rem;
        border-left: 3px solid #f5576c;
    }

    .history-item.win {
        border-left-color: #43e97b;
        background: rgba(67, 233, 123, 0.05);
    }

    .item-result {
        font-weight: 600;
        color: #e2e8f0;
    }
    .item-change {
        font-weight: 700;
        color: #f5576c;
    }
    .history-item.win .item-change {
        color: #43e97b;
    }

    .empty-history {
        text-align: center;
        color: #64748b;
        font-style: italic;
        padding: 1rem;
    }

    .actions {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .reset-btn {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #94a3b8;
        padding: 0.8rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .reset-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        color: white;
        border-color: white;
    }

    .home-btn {
        text-align: center;
        color: #64748b;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .home-btn:hover {
        color: white;
    }

    .chart-container {
        margin-top: 3rem;
        width: 100%;
        height: 200px;
        background: rgba(0, 0, 0, 0.2);
        padding: 1rem;
        border-radius: 12px;
        position: relative;
    }
</style>
