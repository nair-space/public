---
phase: planning
title: Project Planning & Task Breakdown
description: Break down work into actionable tasks and estimate timeline
---

# Project Planning & Task Breakdown

## Milestones

**What are the major checkpoints?**

[ ] Milestone 1: The "Genesis" Foundation - Local PostgreSQL 17 schema finalized, Supabase Auth integrated, and the Glassmorphism layout shell ready.

[ ] Milestone 2: Modular Input Suite - Completion of all individual input pages (Fitrah, Maal, Fidyah, Infaq, Shodaqoh) with unique ID nota input validation.

[ ] Milestone 3: Financial Engine & Amil Management - Implementation of the automated Amil fee logic and the "Lihat Laporan" real-time balance calculations.

[ ] Milestone 4: Audit & Export Readiness - Finalization of the PDF (Cetak Laporan) and Excel (Import/Export) features.

[ ] Milestone 5: Production Handover - Successful manual deployment to Vercel/Supabase and verification of RLS policies.

## Task Breakdown

**What specific work needs to be done?**

### Phase 1: Foundation

[ ] Task 1.1: Local Environment Sync - Setup local PostgreSQL 17 with a shared sequence for ID nota input.

[ ] Task 1.2: UI Architecture - Build the SvelteKit layout with the Glassmorphism modular sidebar.

[ ] Task 1.3: Security Layer - Configure Supabase RLS policies and Rate Limiting for the login/input forms.

### Phase 2: Core Features

[ ] Task 2.1: Income Modules - Create forms for Zakat Fitrah (Uang/Beras), Maal, Fidyah, Infaq, and Shodaqoh.
[ ] Task 2.2: Pengeluaran System - Develop the auto-generating pengeluaran_id logic and outcome tracking.
[ ] Task 2.3: Amil Logic - Build the attendance (1-5) and fee calculation module ($FeeTotal = Absen \times FeeDasar$).

### Phase 3: Integration & Polish

[ ] Task 3.1: Dashboard Visualization - Implement simple charts comparing total income vs. outcome.

[ ] Task 3.2: The "Lihat Laporan" View - Create the unified grid for transaction auditing and net balance calculation.

[ ] Task 3.3: Reporting & Exports - Integrate jsPDF for the 18-field report and SheetJS for CSV/XLSX.

## Dependencies

**What needs to happen in what order?**

Database Sequence First: The shared ID nota input sequence must be defined in PG17 before any input modules are built.

RLS before Deployment: Row Level Security must be tested locally using the "Production User" role simulation before pushing to Supabase.

Logic before UI: The financial sum/subtract logic should be validated in SQL views before binding to the "Lihat Laporan" Svelte page.

## Timeline & Estimates

**When will things be done?**

Phase 1 (Foundation): 1 week.

Phase 2 (Core Features): 2 weeks (Modular pages allow for parallel development).

Phase 3 (Integration): 1 week.

Buffer: 3 days for manual deployment verification and RLS debugging.

## Risks & Mitigation

**What could go wrong?**

Technical Risk: ID nota input collisions if multiple tables aren't hitting the same sequence.

Mitigation: Use a single master PostgreSQL SEQUENCE for all transaction types.

Human Error (Manual Deploy): Accidental push to the wrong environment or skipping a migration.

Mitigation: Strict adherence to the Line Of Genesis manual deployment checklist (no auto-push).

Security Risk: Rate limiting might block legitimate high-volume amil entries.

Mitigation: Whitelist specific Amil IPs or adjust thresholds for authenticated users.

## Resources Needed

**What do we need to succeed?**

Team: 1 SvelteKit Developer, 1 DB Admin (Line Of Genesis Lead).

Tools: SvelteKit, Supabase, PostgreSQL 17 (Local), Tailwind CSS.

Infrastructure: Vercel (Frontend), Supabase (Backend/Database).

Documentation: Audit trail of all ID nota input generation logic.
