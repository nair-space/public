# Deployment Checklist (Vercel + Supabase)

## 1) Supabase
1. Create a Supabase project (pick the closest region).
2. Get the Postgres connection string and set `DATABASE_URL`.
3. Run migrations from your local machine:
   - `npm run db:push` or your standard migration workflow.
4. Verify tables exist:
   - `zakat_fitrah`, `zakat_maal`, `fidyah`, `infaq`, `shodaqoh`, `pengeluaran`, `amil`, `amil_settings`, and auth tables.
5. Seed initial admin user if needed.

## 2) Environment Variables
Set these in Vercel Project Settings:
- `DATABASE_URL`
- `BETTER_AUTH_SECRET`
- `ORIGIN` (production URL)
- `NODE_ENV=production`

Local dev uses `.env` (copy from `.env.example`).

## 3) Vercel
1. Import the GitHub repo into Vercel.
2. Framework preset: **SvelteKit**.
3. Build command: `npm run build`.
4. Output: default SvelteKit (no custom output needed).
5. Set the env vars listed above.

## 4) Auth / Session
1. Confirm `ORIGIN` matches your Vercel domain.
2. Confirm `BETTER_AUTH_SECRET` is set.
3. Test login and session persistence.

## 5) Smoke Tests After Deploy
1. Login with admin and amil.
2. Create/edit/delete each transaction type.
3. Import/export backup.
4. Cetak laporan PDF.
5. Lihat laporan totals.

## 6) Rollback
1. Keep prior Vercel deployments for rollback.
2. Snapshot database before large imports.
