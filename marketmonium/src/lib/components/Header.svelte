<script lang="ts">
    import { onMount } from "svelte";

    let scrollY = 0;
    let isScrolled = false;

    onMount(() => {
        const handleScroll = () => {
            scrollY = window.scrollY;
            isScrolled = scrollY > 50;
        };

        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    });
    import { currentLang, t } from "../stores/translations.js";
    import "./Header.css";

    function toggleLanguage() {
        currentLang.update((l: string) => (l === "en" ? "id" : "en"));
    }
</script>

<header class="header" class:scrolled={isScrolled}>
    <div class="container">
        <div class="header-content">
            <a href="/" class="logo">
                <span class="logo-text">{$t.nav.brand}</span>
            </a>

            <nav class="nav">
                <a href="/trading-plan" class="nav-link">{$t.nav.tradingPlan}</a
                >
                <a href="/trading-journal" class="nav-link"
                    >{$t.nav.tradingJournal}</a
                >
                <a href="/quiz" class="nav-link">{$t.nav.quiz}</a>
                <button
                    class="lang-btn"
                    onclick={toggleLanguage}
                    title="Switch Language"
                >
                    {#if $currentLang === "en"}
                        🇺🇸
                    {:else}
                        🇮🇩
                    {/if}
                </button>
            </nav>
        </div>
    </div>
</header>

<style>
    .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        background: rgba(15, 20, 25, 0.85);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 1.5rem 0;
    }

    .header.scrolled {
        padding: 0.8rem 0;
        background: rgba(15, 20, 25, 0.95);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        text-decoration: none;
        transition: transform 0.3s ease;
    }

    .logo:hover {
        transform: scale(1.05);
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: font-size 0.4s ease;
    }

    .header.scrolled .logo-text {
        font-size: 1.3rem;
    }

    .nav {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .nav-link {
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link::after {
        content: "";
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        transition: width 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .nav-link:hover {
        color: #3b82f6;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 0 1.5rem;
        }

        .nav {
            gap: 1rem;
        }

        .nav-link {
            font-size: 0.9rem;
        }

        .header.scrolled .logo-text {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .nav {
            gap: 0.5rem;
        }

        .nav-link:not(.btn-nav) {
            display: none;
        }

        .logo-text {
            font-size: 1.2rem;
        }
    }

    .lang-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.2s;
    }

    .lang-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }
</style>
