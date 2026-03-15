---
phase: deployment
title: Deployment Strategy
description: Define deployment process, infrastructure, and release procedures
---

# Deployment Strategy

## Infrastructure

Where will the application run?

Hosting platform: Supabase (PostgreSQL 17, Auth, RLS) for the backend/database and Vercel for the SvelteKit frontend.

Environment separation: - Production User: Full CRUD access to operational data. Restricted from altering table structures to ensure data integrity.

Development User: Read-only access to existing data records but holds DDL (Data Definition Language) privileges to manage, create, or modify tables. This prevents accidental DELETE or UPDATE commands on live data while allowing schema evolution.

## Deployment Pipeline

How do we deploy changes?

## Build Process

Local Environment: Development happens on a standard PostgreSQL 17 installation (No Docker just local postgresql).

Environment Config: Managed via .env.local. Local development connects directly to the system-level PostgreSQL service.

## CI/CD Pipeline

Deployment automation: Strictly Manual. Automation is disabled to maintain total control.

Source Control: Code is pushed to GitHub only after manual verification.

Production Push: Frontend is deployed via Vercel CLI or manual dashboard trigger. Database schema changes are applied to Supabase manually after local testing.

## Environment Configuration

What settings differ per environment?

## Development

Configuration: PUBLIC_SUPABASE_URL and PUBLIC_SUPABASE_ANON_KEY point to the local instance or a dedicated dev-project.

Local setup: Standard SvelteKit npm run dev workflow. Database accessed via local port 5432.

## Staging

(Optional for this project): If used, it mirrors Production settings but uses a "Staging" branch in Vercel.

## Production

Configuration: Production-grade Supabase credentials.

Monitoring setup: Supabase built-in logs for RLS violations and Vercel Analytics for frontend performance.

## Deployment Steps

What's the release process?

Pre-deployment checklist: - Run local tests to ensure Nota ID uniqueness across modular tables.

Verify RLS policies are active on new tables.

## Deployment execution steps:

Apply SQL migrations to Supabase Production using the Development User (DDL).

Deploy SvelteKit build to Vercel manually.

Post-deployment validation:

Verify login page glassmorphism renders correctly.

Test one "Zakat Fitrah" entry to confirm Nota ID generation.

## Rollback procedure:

Revert Vercel deployment to the previous stable build.

Manually revert DB schema changes if necessary.

## Database Migrations

How do we handle schema changes?

Migration strategy: Local-first. All changes are tested on local PG17, then the SQL script is executed on Supabase.

Backup procedures: Manual snapshots of the Supabase database before applying structural changes.

Rollback approach: Maintain a "down" migration script for every schema change.

## Secrets Management

How do we handle sensitive data?

Environment variables: Stored in .env (excluded from Git) for local; encrypted in Vercel Dashboard for production.

Secret storage solution: Vercel Environment Variables and Supabase Vault.

Key rotation strategy: Manual rotation of Supabase Service Role keys every 6 months or upon team changes.

Rollback Plan
What if something goes wrong?

Rollback triggers: Failed login attempts, RLS errors in production, or UI breakage in glassmorphism components.

Rollback steps: 1. Instant rollback of Frontend via Vercel "Redeploy" feature. 2. Restoration of DB snapshot if data corruption occurs.

Communication plan: Internal notification within Line Of Genesis to pause all manual pushes until the issue is resolved.
