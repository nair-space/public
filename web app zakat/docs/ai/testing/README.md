---
phase: testing
title: Testing Strategy
description: Define testing approach, test cases, and quality assurance
---

# Testing Strategy

## Test Coverage Goals

**What level of testing do we aim for?**

Unit test coverage: 100% for calculation logic (Amil fees and Zakat balance sums).

Integration test scope: Focus on the "Nota ID" uniqueness across the Fitrah, Maal, Fidyah, Infaq, and Shodaqoh modules.

End-to-end test scenarios: Successful login -> Input transaction -> Check "Lihat Laporan" for correct net total update.

Acceptance Criteria: Zero tolerance for duplicate Nota IDs or negative balances in the reporting view.

## Unit Tests

**What individual components need testing?**

Amil Fee Calculator
[ ] Test case 1: Verify $FeeTotal = Absen \times FeeDasar$ (e.g., $5 \times 1,000,000 = 5,000,000$).
[ ] Test case 2: Handle edge cases where absen is outside the 1-5 range (should default or throw error).
[ ] Additional coverage: Verify rounding behavior for any partial currency conversions.
Currency Formatter Utility
[ ] Test case 1: Convert BIGINT 4000000 to string Rp.4.000.000.
[ ] Test case 2: Handle input 0 and null values gracefully without breaking the Glassmorphism UI.

## Integration Tests

**How do we test component interactions?**

[ ] Nota ID Conflict: Attempt to insert a Zakat Maal record with an existing Zakat Fitrah id_nota_input. (Expect: Postgres unique constraint violation).

[ ] RLS Security: Attempt to fetch the "Lihat Laporan" data using an unauthenticated Supabase client. (Expect: Empty array or 403 Forbidden).

[ ] Database Trigger/View: Verify that inserting an income row automatically updates the "Total Balance" View calculation.

[ ] Rollback Scenario: Simulate a network failure during a "Pengeluaran" entry to ensure the pengeluaran_id is not orphaned.

## End-to-End Tests

**What user flows need validation?**

[ ] Flow 1 (The Zakat Cycle): Log in as Amil -> Enter Zakat Fitrah (Uang) -> Navigate to "Lihat Laporan" -> Confirm the total_zakat_fitrah_uang increased by the correct amount.

[ ] Flow 2 (Reporting): Generate a PDF from "Cetak Laporan" and verify all 18 fields are populated with the correct data from the database.

[ ] Critical Path: Verify the "Logout" button successfully destroys the session and redirects to the Login page.

## Test Data

**What data do we use for testing?**

Test fixtures: JSON files mimicking valid income and outcome objects.

Seed data: A set of 5 Amil IDs with varying attendance scores (1-5) and base fees.

Test Database: A dedicated zakat_testing schema in the local PostgreSQL 17 instance to avoid cluttering the development environment.

## Test Reporting & Coverage

**How do we verify and communicate test results?**

Coverage: Run npm run test:coverage (using Vitest or Playwright).

Thresholds: Minimum 90% coverage for the /src/lib/logic folder.

Manual Sign-off: Every manual deployment to Vercel/Supabase requires a signed-off "Smoke Test" checklist from the Line Of Genesis lead.

## Manual Testing

**What requires human validation?**

Glassmorphism UI: Verify backdrop-blur transparency and readability across Chrome, Firefox, and Safari.

Responsive Sidebar: Ensure the sidebar is usable on mobile devices during field-input (Zakat collection in the field).

PDF Layout: Visual check of the "Cetak Laporan" output for alignment and font clarity.

## Performance Testing

**How do we validate performance?**

Load Testing: Simulate 50 concurrent Amil users entering data to ensure Supabase Rate Limiting doesn't trigger for legitimate users.

Aggregation Speed: Measure the load time of "Lihat Laporan" when the database reaches 10,000+ transaction rows.

## Bug Tracking

**How do we manage issues?**

Issue Tracking: Managed via GitHub Issues or internal Line Of Genesis Trello/Jira.

Severity Levels: - P0 (Critical): Calculation errors, duplicate Nota IDs, RLS bypass.

P1 (High): PDF generation failure, Login issues.

P2 (Minor): Glassmorphism visual glitches, UI alignment.
