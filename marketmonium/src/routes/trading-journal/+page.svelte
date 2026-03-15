<script lang="ts">
    import { onMount } from "svelte";
    import { t } from "$lib/stores/translations.js";

    let isVisible = false;

    onMount(() => {
        isVisible = true;
    });

    $: entries = $t.tradingJournal.logEntries;
    $: reminders = $t.tradingJournal.reminders.points;

    function handleDownload() {
        const header =
            "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
            "xmlns:w='urn:schemas-microsoft-com:office:word' " +
            "xmlns='http://www.w3.org/TR/REC-html40'>" +
            `<head><meta charset='utf-8'><title>${$t.tradingJournal.title}</title></head><body>`;

        const footer = "</body></html>";

        let content = `
            <h1 style="text-align:center; color:#333;">${$t.tradingJournal.title} Log</h1>
            <br/>
            ${entries
                .map(
                    (entry: any) => `
                <div style="border:1px solid #ccc; padding:20px; margin-bottom:20px;">
                    <h2 style="color:#2563eb;">Date: ${entry.date}</h2>
                    ${entry.sections
                        .map(
                            (section: any) => `
                        <div style="margin-bottom:15px;">
                            <h3 style="color:#4b5563; font-size:14px; text-transform:uppercase;">${section.title}</h3>
                            <p style="margin:5px 0 15px;">${section.content}</p>
                        </div>
                    `,
                        )
                        .join("")}
                </div>
            `,
                )
                .join("")}
            
            <div style="margin-top:40px; border-top:2px solid #333; padding-top:20px;">
                <h2 style="color:#7c3aed;">${$t.tradingJournal.reminders.title}</h2>
                <ul>
                    ${reminders.map((r: string) => `<li>${r}</li>`).join("")}
                </ul>
            </div>
        `;

        const sourceHTML = header + content + footer;

        const source =
            "data:application/vnd.ms-word;charset=utf-8," +
            encodeURIComponent(sourceHTML);

        const fileDownload = document.createElement("a");
        document.body.appendChild(fileDownload);
        fileDownload.href = source;
        fileDownload.download = "trading-journal-log.doc";
        fileDownload.click();
        document.body.removeChild(fileDownload);
    }
</script>

<svelte:head>
    <title>{$t.tradingJournal.title} | {$t.nav.brand}</title>
</svelte:head>

<div class="page-container">
    <div class="content-wrapper" class:visible={isVisible}>
        <header class="journal-header">
            <h1 class="main-title">{$t.tradingJournal.title}</h1>
            <h2 class="sub-title gradient-text">
                {$t.tradingJournal.subtitle}
            </h2>
            <div class="divider"></div>
        </header>

        <div class="journal-grid">
            <!-- Journal Entries -->
            <div class="entries-column">
                {#each entries as entry}
                    <article class="journal-entry card">
                        <div class="entry-date">
                            <span class="icon">📅</span>
                            {entry.date}
                        </div>

                        <div class="entry-content">
                            {#each entry.sections as section}
                                <div class="journal-section">
                                    <h4>{section.title}</h4>
                                    <p>{section.content}</p>
                                </div>
                            {/each}
                        </div>
                    </article>
                {/each}
            </div>

            <!-- Reminders Sidebar -->
            <aside class="sidebar-column">
                <div class="card reminders-card">
                    <h3>
                        <span class="icon">🧘‍♂️</span>
                        {$t.tradingJournal.reminders.title}
                    </h3>
                    <ul class="reminders-list">
                        {#each reminders as reminder}
                            <li>{reminder}</li>
                        {/each}
                    </ul>
                </div>
            </aside>
        </div>

        <div class="action-area">
            <button class="btn-download" onclick={handleDownload}>
                <span class="icon">📝</span>
                {$t.actions.downloadJournal}
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
                circle at 80% 10%,
                rgba(240, 147, 251, 0.05) 0%,
                transparent 40%
            ),
            radial-gradient(
                circle at 20% 90%,
                rgba(245, 87, 108, 0.05) 0%,
                transparent 40%
            );
    }

    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease;
    }

    .content-wrapper.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .journal-header {
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
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .divider {
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #f093fb, #f5576c);
        margin: 1.5rem auto 0;
        border-radius: 2px;
    }

    .journal-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 2.5rem;
        backdrop-filter: blur(10px);
    }

    .journal-entry {
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .journal-entry::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #f093fb, #f5576c);
    }

    .entry-date {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .journal-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .journal-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    h4 {
        color: #94a3b8;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0 0 0.8rem 0;
    }

    p {
        margin: 0;
        line-height: 1.8;
        color: #e2e8f0;
        font-size: 1.05rem;
    }

    /* Sidebar Reminders */
    .reminders-card h3 {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-size: 1.3rem;
        margin: 0 0 1.5rem 0;
        color: #ffffff;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .reminders-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .reminders-list li {
        margin-bottom: 1.2rem;
        padding-left: 1.5rem;
        position: relative;
        color: #cbd5e1;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .reminders-list li::before {
        content: "•";
        color: #f5576c;
        position: absolute;
        left: 0;
        font-weight: bold;
    }

    /* Action Area */
    .action-area {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        margin-top: 4rem;
    }

    .btn-download {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        box-shadow: 0 10px 25px rgba(245, 87, 108, 0.4);
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
    @media (max-width: 900px) {
        .journal-grid {
            grid-template-columns: 1fr;
        }

        .page-container {
            padding: 5rem 1rem 2rem;
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
</style>
