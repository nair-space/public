---
phase: implementation
title: Implementation Guide
description: Technical implementation notes, patterns, and code guidelines
---

# Implementation Guide

## Development Setup

**How do we get started?**

Prerequisites: Node.js (LTS), PostgreSQL 17 (Local), and Supabase CLI for managing local migrations.

Environment Setup: 1. Clone the repository (do not push to GitHub/Production until manually verified). 2. Install dependencies: npm install. 3. Initialize local PostgreSQL 17 database.

Configuration: Create a .env file containing:

DATABASE_URL (Local PG17)

PUBLIC_SUPABASE_URL & PUBLIC_SUPABASE_ANON_KEY

SUPABASE_SERVICE_ROLE_KEY (Used only for administrative tasks/Dev User).

## Code Structure

**How is the code organized?**

Directory Structure:

/src/lib/components: Modular UI building blocks (Glassmorphism cards, Sidebar, Input groups).

/src/lib/supabase: Supabase client initialization and RLS helper functions.

/src/lib/utils: Currency formatters (converting 4000000 to Rp.4.000.000) and PDF generators.

/src/routes/(auth): Login and session management.

/src/routes/(app): The core modular dashboard routes (/fitrah, /maal, /amil, etc.).

Naming Conventions: Use PascalCase for Svelte components and camelCase for logic/variables. Financial fields must strictly match database names (e.g., zakat_fitrah_uang_income).

## Implementation Notes

**Key technical details to remember:**

### Core Features

Unique Nota ID Logic: To ensure the ID is unique across all tables, we use a shared PostgreSQL Sequence or a Master Transaction table.
Every "Input" button triggers a transaction that reserves the ID before writing to the specific modular table (Fitrah, Maal, etc.).
Amil Fee Calculation: The fee calculation is reactive.$$FeeTotal = Absen (1-5) \times FeeDasar$$This logic is implemented as a generated column in PostgreSQL or a Svelte Derived Store to ensure real-time UI updates.
Net Balance Logic:The system performs a server-side aggregation:$$\text{Total Net} = \sum(\text{Income Fields}) - \sum(\text{Outcome Fields})$$

### Patterns & Best Practices

Glassmorphism CSS: Utilize Tailwind’s backdrop-blur-md and bg-white/10 with thin white borders (border-white/20) to achieve the simple, clean aesthetic requested.
Component Modularity: Each Zakat type is a standalone Svelte component. This allows "Line Of Genesis" to add new Zakat categories (like Zakat Profesi) by simply duplicating and modifying a module.

## Integration Points

**How do pieces connect?**

Database Connections: SvelteKit uses the @supabase/supabase-js client. Local dev connects to PG17 via standard connection strings.

Export/Import: - SheetJS (XLSX/CSV): Handles the logic for the import/export page.

jsPDF: Specifically configured for the cetak laporan page to render the 18+ data fields into a clean PDF layout.

## Error Handling

**How do we handle failures?**

Validation: Use Zod for schema validation on all inputs (ensuring Rp amounts are integers and weight is decimal).

RLS Violations: Gracefully catch "Permission Denied" errors from Supabase if a user tries to delete records they shouldn't.

Rollback: Database transactions are used for inputs to ensure that if a Nota ID is generated but the data write fails, the ID is not "lost" or orphaned.

## Performance Considerations

**How do we keep it fast?**

Optimization strategies: Heavy calculations (like the 18-field summation for reports) are performed server-side or via PostgreSQL Views to prevent the browser from freezing.

Caching approach: Supabase RLS handles caching implicitly. For the static "Dashboard" charts, data is fetched once per session.

Query optimization: We avoid SELECT \* on the main tables. Instead, we fetch only the specific columns needed for the current view (e.g., just the income fields for the Fitrah page).

Resource management: The app uses SvelteKit's built-in SSR. We ensure that database connections are closed after each request to prevent memory leaks.

Query Optimization: Instead of calculating totals on the frontend, create PostgreSQL Views for the "Lihat Laporan" page. This shifts the heavy lifting to the database.

Asset Loading: Use SvelteKit's preloading for the modular sidebar links to make the dashboard feel instantaneous.

## Security Notes

**What security measures are in place?**

Row Level Security (RLS): Policies are enforced so that authenticated users can insert/read, but only the Dev User role can modify table structures.

Input Validation: Strict sanitization to prevent SQL injection, despite using an ORM/Client library.

Secrets: All Supabase keys are managed via Vercel’s encrypted environment variables in production.
