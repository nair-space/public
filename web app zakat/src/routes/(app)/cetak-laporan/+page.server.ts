import type { Actions, PageServerLoad } from './$types';
import { fail } from '@sveltejs/kit';
import { db } from '$lib/server/db';
import { laporanDaily } from '$lib/server/db/schema';
import { ensureSummaryBackfill } from '$lib/server/db/summary';
import { and, gte, lte, sum } from 'drizzle-orm';

type Totals = {
	income: {
		fitrahUang: number;
		fitrahBeras: number;
		maalUang: number;
		fidyahUang: number;
		fidyahBeras: number;
		infaqUang: number;
		shodaqohUang: number;
	};
	outcome: {
		fitrahUang: number;
		fitrahBeras: number;
		maalUang: number;
		fidyahUang: number;
		fidyahBeras: number;
		infaqUang: number;
		shodaqohUang: number;
	};
};

type ReportPayload = {
	success: true;
	range: { from: string; to: string };
	totals: Totals;
};

const REPORT_CACHE_TTL_MS = 7_200_000;
const reportCache = new Map<string, { expiresAt: number; payload: ReportPayload }>();

function toNumber(value: unknown): number {
	if (typeof value === 'number') return value;
	if (typeof value === 'string') return Number(value) || 0;
	return 0;
}

export const load: PageServerLoad = async () => ({});

export const actions: Actions = {
	compute: async ({ request, locals }) => {
		if (!locals.session) {
			return fail(401, { error: 'Unauthorized' });
		}

		const formData = await request.formData();
		const dateFrom = String(formData.get('date_from') || '');
		const dateTo = String(formData.get('date_to') || '');

		if (!dateFrom || !dateTo) {
			return fail(400, { error: 'Tanggal wajib diisi' });
		}

		const start = new Date(dateFrom);
		const end = new Date(dateTo);
		start.setHours(0, 0, 0, 0);
		end.setHours(23, 59, 59, 999);
		const startDay = start.toISOString().slice(0, 10);
		const endDay = end.toISOString().slice(0, 10);

		const cacheKey = `${locals.user?.id ?? 'anon'}:${dateFrom}:${dateTo}`;
		const cached = reportCache.get(cacheKey);
		if (cached && cached.expiresAt > Date.now()) {
			return cached.payload;
		}
		if (cached) {
			reportCache.delete(cacheKey);
		}

		await ensureSummaryBackfill();

		const [summaryTotals] = await db
			.select({
				fitrahUangIncome: sum(laporanDaily.fitrahUangIncome),
				fitrahBerasIncome: sum(laporanDaily.fitrahBerasIncome),
				maalUangIncome: sum(laporanDaily.maalUangIncome),
				fidyahUangIncome: sum(laporanDaily.fidyahUangIncome),
				fidyahBerasIncome: sum(laporanDaily.fidyahBerasIncome),
				infaqUangIncome: sum(laporanDaily.infaqUangIncome),
				shodaqohUangIncome: sum(laporanDaily.shodaqohUangIncome),
				fitrahUangOutcome: sum(laporanDaily.fitrahUangOutcome),
				fitrahBerasOutcome: sum(laporanDaily.fitrahBerasOutcome),
				maalUangOutcome: sum(laporanDaily.maalUangOutcome),
				fidyahUangOutcome: sum(laporanDaily.fidyahUangOutcome),
				fidyahBerasOutcome: sum(laporanDaily.fidyahBerasOutcome),
				infaqUangOutcome: sum(laporanDaily.infaqUangOutcome),
				shodaqohUangOutcome: sum(laporanDaily.shodaqohUangOutcome)
			})
			.from(laporanDaily)
			.where(and(gte(laporanDaily.day, startDay), lte(laporanDaily.day, endDay)));

		const totals: Totals = {
			income: {
				fitrahUang: toNumber(summaryTotals?.fitrahUangIncome),
				fitrahBeras: toNumber(summaryTotals?.fitrahBerasIncome),
				maalUang: toNumber(summaryTotals?.maalUangIncome),
				fidyahUang: toNumber(summaryTotals?.fidyahUangIncome),
				fidyahBeras: toNumber(summaryTotals?.fidyahBerasIncome),
				infaqUang: toNumber(summaryTotals?.infaqUangIncome),
				shodaqohUang: toNumber(summaryTotals?.shodaqohUangIncome)
			},
			outcome: {
				fitrahUang: toNumber(summaryTotals?.fitrahUangOutcome),
				fitrahBeras: toNumber(summaryTotals?.fitrahBerasOutcome),
				maalUang: toNumber(summaryTotals?.maalUangOutcome),
				fidyahUang: toNumber(summaryTotals?.fidyahUangOutcome),
				fidyahBeras: toNumber(summaryTotals?.fidyahBerasOutcome),
				infaqUang: toNumber(summaryTotals?.infaqUangOutcome),
				shodaqohUang: toNumber(summaryTotals?.shodaqohUangOutcome)
			}
		};

		const payload: ReportPayload = {
			success: true,
			range: { from: dateFrom, to: dateTo },
			totals
		};

		reportCache.set(cacheKey, { expiresAt: Date.now() + REPORT_CACHE_TTL_MS, payload });

		return payload;
	}
};
