---
phase: requirements
title: Requirements & Problem Understanding
description: Clarify the problem space, gather requirements, and define success criteria
---

# Requirements & Problem Understanding

## Problem Statement

**What problem are we solving?**

The Core Problem: Managing diverse Islamic charitable funds (Zakat Fitrah, Maal, Fidyah, Infaq, Shodaqoh) often results in fragmented data, inconsistent "Nota" numbering, and manual calculation errors for Amil fees.

Affected Parties: Amil (administrators) who input data and Volunteers who track distributions.

Current Situation: Likely manual bookkeeping or disparate spreadsheets that do not offer real-time "Net Balance" visibility or secure, role-based access to the database.

## Goals & Objectives

**What do we want to achieve?**

Primary Goals: - Centralized modular system for all Zakat-related transactions.
Automated "Net Balance" tracking ($Income - Outcome$) for every fund category.
Secure local-to-cloud deployment workflow via Line Of Genesis standards.
Secondary Goals: - Integrated Amil fee management based on activity (Absen 1-5).
One-click PDF reporting for all 18+ required financial fields.
Non-goals: - Integrated online payment gateways (this is an internal management and tracking tool).Public-facing donation portals.

## User Stories & Use Cases

**How will users interact with the solution?**

As an Amil (Admin), I want to input Zakat Fitrah (Uang & Beras) using a unique Nota ID so that the audit trail is preserved.

As a Manager, I want to view the "Lihat Laporan" page to see total income vs. outcome across all categories at a glance and doing all the input also.

As a Developer (Line Of Genesis), I want a read-only production user and a DDL-capable dev user to prevent accidental data deletion.

Key Workflow: 1. Login via Glassmorphism UI. 2. Select module (e.g., Zakat Maal). 3. Input data. 4. Verify calculation in Dashboard.

## Success Criteria

**How will we know when we're done?**

Uniqueness: No two transactions share an ID nota input, regardless of the module (Fitrah, Maal, etc.).

Accuracy: The total_zakat_fitrah_uang (and others) accurately reflects the sum of all income minus the sum of all outcomes.

Functionality: Successful export of a PDF containing all 18 specified data fields.

Security: RLS policies successfully block unauthenticated access and prevent "Production" users from dropping tables.

## Constraints & Assumptions

**What limitations do we need to work within?**

Technical Constraints: - Must run on PostgreSQL 17 (local) and Supabase (production).

Manual deployment only (No GitHub Auto-push).

Business Constraints: Currency must be stored as BIGINT integers (e.g., 4000000 for Rp.4M) for precision.

Assumptions: - Amil attendance (1-5) is the only variable influencing the fee_total_amil.

One Nota ID will only ever be associated with one specific transaction type.

## Questions & Open Items

**What do we still need to clarify?**

ID Reset: Should the ID nota input sequence reset annually or continue indefinitely?

User Roles: Will there be a "Super Admin" role that can delete records, or is deletion strictly forbidden for everyone in the Production UI?

Amil Fee Payouts: Does the fee_total_amil need to be tracked as an "Outcome" automatically once calculated, or is it a separate report?
