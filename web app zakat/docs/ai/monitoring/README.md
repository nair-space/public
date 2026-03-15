---
phase: monitoring
title: Monitoring & Observability
description: Define monitoring strategy, metrics, alerts, and incident response
---

# Monitoring & Observability

## Key Metrics

**What do we need to track?**

### Performance Metrics

Performance Metrics
SvelteKit SSR Latency: Time taken to render the Glassmorphism dashboard on Vercel.

Supabase Query Performance: Execution time for the "Lihat Laporan" aggregation queries (Sum of Income - Sum of Outcome).

Edge Function Duration: If using Vercel/Supabase functions for PDF generation.

### Business Metrics

Total Funds per Category: Real-time monitoring of total_zakat_fitrah_uang, total_fidyah_beras, etc.

Amil Productivity: Distribution of absen_amil scores across the organization.

Transaction Volume: Number of unique ID nota input generated per day.

### Error Metrics

DB Constraint Violations: Tracking attempts to insert duplicate ID nota input or pengeluaran_id.

RLS Denials: Logging when a user (Production vs. Dev) attempts an unauthorized action.

Calculation Mismatches: Discrepancies between table-summed totals and cached dashboard values.

## Monitoring Tools

**What tools are we using?**

Application Monitoring (APM): Vercel Analytics for frontend vitals and deployment health.

Infrastructure Monitoring: Supabase Observability for PostgreSQL 17 CPU/Memory and disk usage.

Log Aggregation: Supabase Postgres Logs for tracking every SQL execution involving financial tables.

Error Tracking: Sentry (optional) for catching SvelteKit runtime exceptions in the Glassmorphism UI.

## Logging Strategy

**What do we log and how?**

Log Levels: - INFO: Successful Nota creation, user login.

WARN: Rate limit triggers, failed login attempts.

ERROR: Duplicate ID collisions, RLS permission failures.

Structured Logging: All financial logs must include amil_id, id_nota_input, and timestamp.

Retention: Financial transaction logs are kept for a minimum of 1 year for auditing.

Sensitive Data: Mask user passwords and PII; only ID nota and amounts are logged in plain text.

## Alerts & Notifications

**When and how do we get notified?**

### Critical Alerts

Negative Balance: Sum(Income) < Sum(Outcome) in any category → Action: Immediate lock on pengeluaran table and notify Admin.

DB Connection Failure: Vercel cannot reach Supabase → Action: Show the error log messages.

### Warning Alerts

Rate Limit Triggered: High frequency of inputs from a single IP → Action: Monitor for brute-force/spam.

High Absen Amil Fees: Unexpectedly high fee_total_amil payouts → Action: Audit amil attendance records.

## Dashboards

**What do we visualize?**

System Health: Vercel deployment status, Supabase API response times.

Financial Pulse: Real-time "Net Total" cards for Fitrah, Maal, Fidyah, Infaq, and Shodaqoh.

Audit Dashboard: View of the most recent 50 ID nota input transactions across all modules.

## Incident Response

**How do we handle issues?**

### On-Call Rotation

Primary: Lead Developer at Line Of Genesis.

Escalation: System Administrator for Supabase Infrastructure.

### Incident Process

Detection: Alert triggers based on financial discrepancy or downtime.

Triage: Determine if the issue is UI (Vercel) or Data (Supabase).

Diagnosis: Check Postgres logs for the specific ID nota that caused the error.

Resolution: Revert to the last stable manual deployment if code-related; apply SQL fix if data-related.

Post-mortem: Document why the manual deployment didn't catch the edge case during local PG17 testing.

## Health Checks

**How do we verify system health?**

Endpoint Health: Periodic ping to the /api/health route in SvelteKit.

Database Check: Simple SELECT 1 on the Zakat tables to ensure PG17 is responsive.

Integrity Smoke Test: Automated script that runs the "Sum(Income) - Sum(Outcome)" logic and compares it against a known test fixture.
