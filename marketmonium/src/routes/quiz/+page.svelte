<script lang="ts">
    import { onMount } from "svelte";
    import { fade, fly } from "svelte/transition";
    import { t } from "$lib/stores/translations.js";

    let isVisible = false;
    let currentQuestionIndex = 0;
    let trendScore = 0;
    let reversionScore = 0;
    let quizCompleted = false;
    let result = "";

    onMount(() => {
        isVisible = true;
    });

    // Make questions reactive to language changes
    $: questions = $t.quiz.questions;

    function handleAnswer(type: string) {
        if (type === "trend") {
            trendScore++;
        } else {
            reversionScore++;
        }

        if (currentQuestionIndex < questions.length - 1) {
            currentQuestionIndex++;
        } else {
            finishQuiz();
        }
    }

    function finishQuiz() {
        quizCompleted = true;
        if (trendScore >= reversionScore) {
            result = "trend";
        } else {
            result = "reversion";
        }
    }

    function retakeQuiz() {
        currentQuestionIndex = 0;
        trendScore = 0;
        reversionScore = 0;
        quizCompleted = false;
        result = "";
    }
</script>

<svelte:head>
    <title>{$t.quiz.title} | {$t.nav.brand}</title>
</svelte:head>

<div class="page-container">
    <div class="content-wrapper" class:visible={isVisible}>
        <header class="quiz-header">
            <h1 class="main-title">{$t.quiz.title}</h1>
            <h2 class="sub-title gradient-text">{$t.quiz.subtitle}</h2>
            <div class="divider"></div>
        </header>

        <div class="quiz-container">
            {#if !quizCompleted}
                <div class="question-card" in:fade={{ duration: 300 }}>
                    <div class="progress-bar">
                        <div
                            class="progress-fill"
                            style="width: {((currentQuestionIndex + 1) /
                                questions.length) *
                                100}%"
                        ></div>
                    </div>
                    <div class="question-count">
                        {$t.quiz.questionCount
                            .replace(
                                "{current}",
                                (currentQuestionIndex + 1).toString(),
                            )
                            .replace("{total}", questions.length.toString())}
                    </div>

                    <h3 class="question-text">
                        {questions[currentQuestionIndex].question}
                    </h3>

                    <div class="options-grid">
                        {#each questions[currentQuestionIndex].options as option}
                            <button
                                class="option-btn"
                                onclick={() => handleAnswer(option.type)}
                            >
                                {option.text}
                            </button>
                        {/each}
                    </div>
                </div>
            {:else}
                <div class="result-card" in:fly={{ y: 50, duration: 500 }}>
                    <div class="result-header">
                        <span class="result-icon"
                            >{result === "trend" ? "📈" : "🔄"}</span
                        >
                        <h3>{$t.quiz.results.youAre}</h3>
                        <h2 class="result-title gradient-text">
                            {result === "trend"
                                ? $t.quiz.results.trendTitle
                                : $t.quiz.results.reversionTitle}
                        </h2>
                    </div>

                    <div class="result-description">
                        {#if result === "trend"}
                            <p>{$t.quiz.results.trendDesc1}</p>
                            <p>{@html $t.quiz.results.trendDesc2}</p>
                        {:else}
                            <p>{$t.quiz.results.reversionDesc1}</p>
                            <p>{@html $t.quiz.results.reversionDesc2}</p>
                        {/if}
                    </div>

                    <div class="action-area">
                        <button class="retake-btn" onclick={retakeQuiz}
                            >{$t.actions.retakeQuiz}</button
                        >
                        <a href="/" class="home-btn">{$t.actions.backHome}</a>
                    </div>
                </div>
            {/if}
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
                circle at 50% 10%,
                rgba(67, 233, 123, 0.05) 0%,
                transparent 40%
            ),
            radial-gradient(
                circle at 50% 90%,
                rgba(56, 249, 215, 0.05) 0%,
                transparent 40%
            );
    }

    .content-wrapper {
        max-width: 800px;
        margin: 0 auto;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease;
    }

    .content-wrapper.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .quiz-header {
        text-align: center;
        margin-bottom: 3rem;
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
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .divider {
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #43e97b, #38f9d7);
        margin: 1.5rem auto 0;
        border-radius: 2px;
    }

    .quiz-container {
        min-height: 400px;
        display: flex;
        justify-content: center;
    }

    .question-card,
    .result-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 3rem;
        backdrop-filter: blur(10px);
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #43e97b, #38f9d7);
        transition: width 0.3s ease;
    }

    .question-count {
        color: #94a3b8;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .question-text {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 2.5rem 0;
        line-height: 1.4;
    }

    .options-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .option-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1.2rem 1.5rem;
        border-radius: 12px;
        color: #e2e8f0;
        font-size: 1.1rem;
        text-align: left;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .option-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #43e97b;
        transform: translateX(5px);
    }

    /* Results */
    .result-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .result-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        display: block;
    }

    .result-card h3 {
        color: #94a3b8;
        font-size: 1.2rem;
        margin: 0 0 0.5rem 0;
    }

    .result-description {
        background: rgba(255, 255, 255, 0.05);
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2.5rem;
        line-height: 1.6;
        color: #e2e8f0;
    }

    .result-description p {
        margin-bottom: 1.5rem;
    }

    .result-description p:last-child {
        margin-bottom: 0;
    }

    .action-area {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .retake-btn {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .retake-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
    }

    .home-btn {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: #0f1419;
        text-decoration: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        transition: transform 0.2s;
        border: none;
    }

    .home-btn:hover {
        transform: translateY(-2px);
    }

    @media (max-width: 600px) {
        .question-card,
        .result-card {
            padding: 2rem 1.5rem;
        }

        .question-text {
            font-size: 1.4rem;
        }

        .action-area {
            flex-direction: column;
        }

        .retake-btn,
        .home-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>
