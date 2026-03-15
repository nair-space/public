---
phase: design
title: System Design & Architecture
description: Define the technical architecture, components, and data models
---

# System Design & Architecture

## Architecture Overview

**What is the high-level system structure?**

graph TD
User((Amil User)) -->|HTTPS| Frontend[SvelteKit App - Vercel]
Frontend -->|Glassmorphism UI| Components[Modular Features]

    subgraph Supabase Backend
        Components -->|Auth / RLS| Client[Supabase Client]
        Client -->|SQL| DB[(PostgreSQL 17)]
        DB -->|Calculations| Views[Summary Views / Reports]
    end

    subgraph "Local Development (Line Of Genesis)"
        LocalDB[(PostgreSQL 17 Local)] -.->|Migration| DB
    end

Key Components:

Modular Frontend: Individual routes for each Zakat type and Amil management, utilizing SvelteKit's file-based routing.

PostgreSQL 17 Engine: Handles core data storage with custom sequences to ensure ID nota input uniqueness across disparate tables.

Logic Layer: Financial calculations (Income - Outcome) handled primarily through SQL Views or SvelteKit load functions to ensure consistency.

Technology Stack Choices:

SvelteKit: Chosen for SSR performance and modularity.

Supabase: Provides the RLS layer and Rate Limiting required for secure web access.

PostgreSQL 17: Essential for local-to-cloud parity and advanced data types.

## Data Models

**What data do we need to manage?**

Core Tables & Fields
All currency fields are stored as BIGINT (e.g., 4000000 for Rp.4.000.000) to avoid floating-point errors. Weights are stored as DECIMAL.
Income Records (Fitrah, Maal, Fidyah, Infaq, Shodaqoh):

id_nota_input: BIGINT (Unique across all tables).

zakat_fitrah_uang_income: BIGINT.

zakat_fitrah_beras_income: DECIMAL.

zakat_maal_uang_income: BIGINT.

fidyah_uang_income: BIGINT.

fidyah_beras_income: DECIMAL.

infaq_uang_income: BIGINT.

shodaqoh_uang_income: BIGINT.

Outcome (Pengeluaran):

pengeluaran_id_input: BIGINT (Auto-generated, Unique).

nama_pengeluaran: TEXT (Short).

zakat\_[type]\_uang_outcome: BIGINT.

zakat\_[type]\_beras_outcome: DECIMAL.

Amil & Volunteer Management:

amil_id: BIGINT (Unique).

nama_amil: TEXT.

absen_amil: INT (1-5).

jatah_amil: BIGINT.

fee_dasar_amil: BIGINT.

fee_total_amil: BIGINT ($absen \times fee\_dasar$).

Calculation Logic (Summary Views)The system calculates net totals in real-time for the Lihat Laporan page:Total Net Formula: $\sum(\text{Income}) - \sum(\text{Outcome}) = \text{Current Balance}$Applied to: Fitrah (Uang/Beras), Maal, Fidyah (Uang/Beras), Infaq, and Shodaqoh.

## API Design

**How do components communicate?**

Internal Interfaces: SvelteKit Server Load Functions fetch data via the Supabase Service Role (for admin reports) or the Anon key (checked by RLS).

Authentication: Supabase Auth (Email/Password) integrated into the Glassmorphism login page.

Authorization (RLS): - Production User: CRUD access on rows but blocked from DROP or TRUNCATE.

Development User: Full schema access on local PG17 for migrations.

## Component Breakdown

**What are the major building blocks?**

Frontend (Modular Sidebar):

Dashboard: Charts showing total income vs. outcome.

Zakat Modules: Separate pages/forms for Fitrah, Maal, Fidyah, Infaq, and Shodaqoh.

Lihat Laporan: A unified data grid showing all transactions and the calculated net balances.

Import/Export: Logic handlers for .csv, .xlsx, and pdf-make for PDF generation.

Database Layer:

Custom PostgreSQL Sequences to ensure the ID nota input never repeats even if data is added to different tables.

## Design Decisions

**Why did we choose this approach?**

Integer for Currency: We store Rp.4.000.000 as 4000000 to ensure $100\%$ precision in sum/subtract operations.
Modular Sidebar: Each feature is a separate Svelte component to allow Line Of Genesis to update specific Zakat logic without affecting others.
Local-First PG17: Ensures that heavy schema changes and testing occur on your local machine before touching the Supabase production environment.

## Non-Functional Requirements

**How should the system perform?**

How should the system perform?

Security:

RLS Enabled: No data is accessible without a valid session.

Rate Limiting: Supabase level limits on API calls to prevent brute-force on the login and input forms.

Performance: Glassmorphism UI components are optimized using CSS will-change to ensure smooth scrolling on the dashboard.

Reliability: The use of a single unique ID across tables ensures auditability for every single "Nota."
