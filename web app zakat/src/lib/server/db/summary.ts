import { sql } from 'drizzle-orm';
import { db } from '$lib/server/db';
import { laporanDaily, laporanMonthly } from '$lib/server/db/schema';

export type SummaryDelta = Partial<{
	fitrahUangIncome: number;
	fitrahBerasIncome: number;
	maalUangIncome: number;
	fidyahUangIncome: number;
	fidyahBerasIncome: number;
	infaqUangIncome: number;
	shodaqohUangIncome: number;
	fitrahUangOutcome: number;
	fitrahBerasOutcome: number;
	maalUangOutcome: number;
	fidyahUangOutcome: number;
	fidyahBerasOutcome: number;
	infaqUangOutcome: number;
	shodaqohUangOutcome: number;
}>;

const summaryKeys = [
	'fitrahUangIncome',
	'fitrahBerasIncome',
	'maalUangIncome',
	'fidyahUangIncome',
	'fidyahBerasIncome',
	'infaqUangIncome',
	'shodaqohUangIncome',
	'fitrahUangOutcome',
	'fitrahBerasOutcome',
	'maalUangOutcome',
	'fidyahUangOutcome',
	'fidyahBerasOutcome',
	'infaqUangOutcome',
	'shodaqohUangOutcome'
] as const;

type SummaryKey = (typeof summaryKeys)[number];

export function toNumber(value: unknown): number {
	if (typeof value === 'number') return value;
	if (typeof value === 'string') return Number(value) || 0;
	return 0;
}

function normalizeDay(date: Date): string {
	const day = new Date(date);
	day.setHours(0, 0, 0, 0);
	return day.toISOString().slice(0, 10);
}

function normalizeMonth(date: Date): string {
	const month = new Date(date);
	month.setDate(1);
	month.setHours(0, 0, 0, 0);
	return month.toISOString().slice(0, 10);
}

function buildDeltaValues(delta: SummaryDelta): Record<SummaryKey, number> {
	const values: Record<SummaryKey, number> = {
		fitrahUangIncome: 0,
		fitrahBerasIncome: 0,
		maalUangIncome: 0,
		fidyahUangIncome: 0,
		fidyahBerasIncome: 0,
		infaqUangIncome: 0,
		shodaqohUangIncome: 0,
		fitrahUangOutcome: 0,
		fitrahBerasOutcome: 0,
		maalUangOutcome: 0,
		fidyahUangOutcome: 0,
		fidyahBerasOutcome: 0,
		infaqUangOutcome: 0,
		shodaqohUangOutcome: 0
	};

	for (const key of summaryKeys) {
		if (delta[key] !== undefined) {
			values[key] = delta[key] as number;
		}
	}

	return values;
}

function toNumericString(value: number): string {
	return value.toString();
}

function toDailyInsert(values: Record<SummaryKey, number>, day: string) {
	return {
		day,
		fitrahUangIncome: values.fitrahUangIncome,
		fitrahBerasIncome: toNumericString(values.fitrahBerasIncome),
		maalUangIncome: values.maalUangIncome,
		fidyahUangIncome: values.fidyahUangIncome,
		fidyahBerasIncome: toNumericString(values.fidyahBerasIncome),
		infaqUangIncome: values.infaqUangIncome,
		shodaqohUangIncome: values.shodaqohUangIncome,
		fitrahUangOutcome: values.fitrahUangOutcome,
		fitrahBerasOutcome: toNumericString(values.fitrahBerasOutcome),
		maalUangOutcome: values.maalUangOutcome,
		fidyahUangOutcome: values.fidyahUangOutcome,
		fidyahBerasOutcome: toNumericString(values.fidyahBerasOutcome),
		infaqUangOutcome: values.infaqUangOutcome,
		shodaqohUangOutcome: values.shodaqohUangOutcome
	};
}

function toMonthlyInsert(values: Record<SummaryKey, number>, month: string) {
	return {
		month,
		fitrahUangIncome: values.fitrahUangIncome,
		fitrahBerasIncome: toNumericString(values.fitrahBerasIncome),
		maalUangIncome: values.maalUangIncome,
		fidyahUangIncome: values.fidyahUangIncome,
		fidyahBerasIncome: toNumericString(values.fidyahBerasIncome),
		infaqUangIncome: values.infaqUangIncome,
		shodaqohUangIncome: values.shodaqohUangIncome,
		fitrahUangOutcome: values.fitrahUangOutcome,
		fitrahBerasOutcome: toNumericString(values.fitrahBerasOutcome),
		maalUangOutcome: values.maalUangOutcome,
		fidyahUangOutcome: values.fidyahUangOutcome,
		fidyahBerasOutcome: toNumericString(values.fidyahBerasOutcome),
		infaqUangOutcome: values.infaqUangOutcome,
		shodaqohUangOutcome: values.shodaqohUangOutcome
	};
}

export async function applySummaryDelta(date: Date, delta: SummaryDelta): Promise<void> {
	const day = normalizeDay(date);
	const month = normalizeMonth(date);
	const values = buildDeltaValues(delta);

	const dailyValues = toDailyInsert(values, day);
	const monthlyValues = toMonthlyInsert(values, month);

	await db.insert(laporanDaily).values(dailyValues).onConflictDoUpdate({
		target: laporanDaily.day,
		set: {
			fitrahUangIncome: sql`${laporanDaily.fitrahUangIncome} + ${values.fitrahUangIncome}`,
			fitrahBerasIncome: sql`${laporanDaily.fitrahBerasIncome} + ${values.fitrahBerasIncome}`,
			maalUangIncome: sql`${laporanDaily.maalUangIncome} + ${values.maalUangIncome}`,
			fidyahUangIncome: sql`${laporanDaily.fidyahUangIncome} + ${values.fidyahUangIncome}`,
			fidyahBerasIncome: sql`${laporanDaily.fidyahBerasIncome} + ${values.fidyahBerasIncome}`,
			infaqUangIncome: sql`${laporanDaily.infaqUangIncome} + ${values.infaqUangIncome}`,
			shodaqohUangIncome: sql`${laporanDaily.shodaqohUangIncome} + ${values.shodaqohUangIncome}`,
			fitrahUangOutcome: sql`${laporanDaily.fitrahUangOutcome} + ${values.fitrahUangOutcome}`,
			fitrahBerasOutcome: sql`${laporanDaily.fitrahBerasOutcome} + ${values.fitrahBerasOutcome}`,
			maalUangOutcome: sql`${laporanDaily.maalUangOutcome} + ${values.maalUangOutcome}`,
			fidyahUangOutcome: sql`${laporanDaily.fidyahUangOutcome} + ${values.fidyahUangOutcome}`,
			fidyahBerasOutcome: sql`${laporanDaily.fidyahBerasOutcome} + ${values.fidyahBerasOutcome}`,
			infaqUangOutcome: sql`${laporanDaily.infaqUangOutcome} + ${values.infaqUangOutcome}`,
			shodaqohUangOutcome: sql`${laporanDaily.shodaqohUangOutcome} + ${values.shodaqohUangOutcome}`
		}
	});

	await db.insert(laporanMonthly).values(monthlyValues).onConflictDoUpdate({
		target: laporanMonthly.month,
		set: {
			fitrahUangIncome: sql`${laporanMonthly.fitrahUangIncome} + ${values.fitrahUangIncome}`,
			fitrahBerasIncome: sql`${laporanMonthly.fitrahBerasIncome} + ${values.fitrahBerasIncome}`,
			maalUangIncome: sql`${laporanMonthly.maalUangIncome} + ${values.maalUangIncome}`,
			fidyahUangIncome: sql`${laporanMonthly.fidyahUangIncome} + ${values.fidyahUangIncome}`,
			fidyahBerasIncome: sql`${laporanMonthly.fidyahBerasIncome} + ${values.fidyahBerasIncome}`,
			infaqUangIncome: sql`${laporanMonthly.infaqUangIncome} + ${values.infaqUangIncome}`,
			shodaqohUangIncome: sql`${laporanMonthly.shodaqohUangIncome} + ${values.shodaqohUangIncome}`,
			fitrahUangOutcome: sql`${laporanMonthly.fitrahUangOutcome} + ${values.fitrahUangOutcome}`,
			fitrahBerasOutcome: sql`${laporanMonthly.fitrahBerasOutcome} + ${values.fitrahBerasOutcome}`,
			maalUangOutcome: sql`${laporanMonthly.maalUangOutcome} + ${values.maalUangOutcome}`,
			fidyahUangOutcome: sql`${laporanMonthly.fidyahUangOutcome} + ${values.fidyahUangOutcome}`,
			fidyahBerasOutcome: sql`${laporanMonthly.fidyahBerasOutcome} + ${values.fidyahBerasOutcome}`,
			infaqUangOutcome: sql`${laporanMonthly.infaqUangOutcome} + ${values.infaqUangOutcome}`,
			shodaqohUangOutcome: sql`${laporanMonthly.shodaqohUangOutcome} + ${values.shodaqohUangOutcome}`
		}
	});
}

type DailySummaryRow = {
	day: string;
	fitrahUangIncome: number;
	fitrahBerasIncome: number;
	maalUangIncome: number;
	fidyahUangIncome: number;
	fidyahBerasIncome: number;
	infaqUangIncome: number;
	shodaqohUangIncome: number;
	fitrahUangOutcome: number;
	fitrahBerasOutcome: number;
	maalUangOutcome: number;
	fidyahUangOutcome: number;
	fidyahBerasOutcome: number;
	infaqUangOutcome: number;
	shodaqohUangOutcome: number;
};

let backfillPromise: Promise<void> | null = null;

function emptyDailyRow(day: string): DailySummaryRow {
	return {
		day,
		fitrahUangIncome: 0,
		fitrahBerasIncome: 0,
		maalUangIncome: 0,
		fidyahUangIncome: 0,
		fidyahBerasIncome: 0,
		infaqUangIncome: 0,
		shodaqohUangIncome: 0,
		fitrahUangOutcome: 0,
		fitrahBerasOutcome: 0,
		maalUangOutcome: 0,
		fidyahUangOutcome: 0,
		fidyahBerasOutcome: 0,
		infaqUangOutcome: 0,
		shodaqohUangOutcome: 0
	};
}

function dayKey(day: string): string {
	return new Date(day).toISOString().slice(0, 10);
}

function monthKey(day: string): string {
	const date = new Date(day);
	date.setDate(1);
	date.setHours(0, 0, 0, 0);
	return date.toISOString().slice(0, 10);
}

export async function ensureSummaryBackfill(): Promise<void> {
	if (backfillPromise) {
		await backfillPromise;
		return;
	}

	const [existing] = await db.execute(sql`select count(*) as count from laporan_daily`);
	const count = Number(existing?.count ?? 0);
	if (count > 0) return;

	backfillPromise = (async () => {
		const dailyMap = new Map<string, DailySummaryRow>();

		const fitrahRows = await db.execute(sql`
			select date(created_at) as day,
				sum(zakat_fitrah_uang_income) as fitrah_uang_income,
				sum(zakat_fitrah_beras_income) as fitrah_beras_income
			from zakat_fitrah
			group by date(created_at)
		`);
		for (const row of fitrahRows) {
			const day = String(row.day);
			const key = dayKey(day);
			const existingRow = dailyMap.get(key) ?? emptyDailyRow(day);
			existingRow.fitrahUangIncome = toNumber(row.fitrah_uang_income);
			existingRow.fitrahBerasIncome = toNumber(row.fitrah_beras_income);
			dailyMap.set(key, existingRow);
		}

		const maalRows = await db.execute(sql`
			select date(created_at) as day,
				sum(zakat_maal_uang_income) as maal_uang_income
			from zakat_maal
			group by date(created_at)
		`);
		for (const row of maalRows) {
			const day = String(row.day);
			const key = dayKey(day);
			const existingRow = dailyMap.get(key) ?? emptyDailyRow(day);
			existingRow.maalUangIncome = toNumber(row.maal_uang_income);
			dailyMap.set(key, existingRow);
		}

		const fidyahRows = await db.execute(sql`
			select date(created_at) as day,
				sum(fidyah_uang_income) as fidyah_uang_income,
				sum(fidyah_beras_income) as fidyah_beras_income
			from fidyah
			group by date(created_at)
		`);
		for (const row of fidyahRows) {
			const day = String(row.day);
			const key = dayKey(day);
			const existingRow = dailyMap.get(key) ?? emptyDailyRow(day);
			existingRow.fidyahUangIncome = toNumber(row.fidyah_uang_income);
			existingRow.fidyahBerasIncome = toNumber(row.fidyah_beras_income);
			dailyMap.set(key, existingRow);
		}

		const infaqRows = await db.execute(sql`
			select date(created_at) as day,
				sum(infaq_uang_income) as infaq_uang_income
			from infaq
			group by date(created_at)
		`);
		for (const row of infaqRows) {
			const day = String(row.day);
			const key = dayKey(day);
			const existingRow = dailyMap.get(key) ?? emptyDailyRow(day);
			existingRow.infaqUangIncome = toNumber(row.infaq_uang_income);
			dailyMap.set(key, existingRow);
		}

		const shodaqohRows = await db.execute(sql`
			select date(created_at) as day,
				sum(shodaqoh_uang_income) as shodaqoh_uang_income
			from shodaqoh
			group by date(created_at)
		`);
		for (const row of shodaqohRows) {
			const day = String(row.day);
			const key = dayKey(day);
			const existingRow = dailyMap.get(key) ?? emptyDailyRow(day);
			existingRow.shodaqohUangIncome = toNumber(row.shodaqoh_uang_income);
			dailyMap.set(key, existingRow);
		}

		const pengeluaranRows = await db.execute(sql`
			select date(created_at) as day,
				sum(zakat_fitrah_uang_outcome) as fitrah_uang_outcome,
				sum(zakat_fitrah_beras_outcome) as fitrah_beras_outcome,
				sum(zakat_maal_uang_outcome) as maal_uang_outcome,
				sum(fidyah_uang_outcome) as fidyah_uang_outcome,
				sum(fidyah_beras_outcome) as fidyah_beras_outcome,
				sum(infaq_uang_outcome) as infaq_uang_outcome,
				sum(shodaqoh_uang_outcome) as shodaqoh_uang_outcome
			from pengeluaran
			group by date(created_at)
		`);
		for (const row of pengeluaranRows) {
			const day = String(row.day);
			const key = dayKey(day);
			const existingRow = dailyMap.get(key) ?? emptyDailyRow(day);
			existingRow.fitrahUangOutcome = toNumber(row.fitrah_uang_outcome);
			existingRow.fitrahBerasOutcome = toNumber(row.fitrah_beras_outcome);
			existingRow.maalUangOutcome = toNumber(row.maal_uang_outcome);
			existingRow.fidyahUangOutcome = toNumber(row.fidyah_uang_outcome);
			existingRow.fidyahBerasOutcome = toNumber(row.fidyah_beras_outcome);
			existingRow.infaqUangOutcome = toNumber(row.infaq_uang_outcome);
			existingRow.shodaqohUangOutcome = toNumber(row.shodaqoh_uang_outcome);
			dailyMap.set(key, existingRow);
		}

		const dailyRows = Array.from(dailyMap.values());
		if (dailyRows.length > 0) {
			const dailyInserts = dailyRows.map((row) =>
				toDailyInsert(
					{
						fitrahUangIncome: row.fitrahUangIncome,
						fitrahBerasIncome: row.fitrahBerasIncome,
						maalUangIncome: row.maalUangIncome,
						fidyahUangIncome: row.fidyahUangIncome,
						fidyahBerasIncome: row.fidyahBerasIncome,
						infaqUangIncome: row.infaqUangIncome,
						shodaqohUangIncome: row.shodaqohUangIncome,
						fitrahUangOutcome: row.fitrahUangOutcome,
						fitrahBerasOutcome: row.fitrahBerasOutcome,
						maalUangOutcome: row.maalUangOutcome,
						fidyahUangOutcome: row.fidyahUangOutcome,
						fidyahBerasOutcome: row.fidyahBerasOutcome,
						infaqUangOutcome: row.infaqUangOutcome,
						shodaqohUangOutcome: row.shodaqohUangOutcome
					},
					row.day
				)
			);
			await db.insert(laporanDaily).values(dailyInserts);
		}

		const monthlyMap = new Map<string, DailySummaryRow>();
		for (const row of dailyRows) {
			const key = monthKey(row.day);
			const existingRow = monthlyMap.get(key) ?? emptyDailyRow(key);
			existingRow.fitrahUangIncome += row.fitrahUangIncome;
			existingRow.fitrahBerasIncome += row.fitrahBerasIncome;
			existingRow.maalUangIncome += row.maalUangIncome;
			existingRow.fidyahUangIncome += row.fidyahUangIncome;
			existingRow.fidyahBerasIncome += row.fidyahBerasIncome;
			existingRow.infaqUangIncome += row.infaqUangIncome;
			existingRow.shodaqohUangIncome += row.shodaqohUangIncome;
			existingRow.fitrahUangOutcome += row.fitrahUangOutcome;
			existingRow.fitrahBerasOutcome += row.fitrahBerasOutcome;
			existingRow.maalUangOutcome += row.maalUangOutcome;
			existingRow.fidyahUangOutcome += row.fidyahUangOutcome;
			existingRow.fidyahBerasOutcome += row.fidyahBerasOutcome;
			existingRow.infaqUangOutcome += row.infaqUangOutcome;
			existingRow.shodaqohUangOutcome += row.shodaqohUangOutcome;
			monthlyMap.set(key, existingRow);
		}

		const monthlyRows = Array.from(monthlyMap.values()).map((row) =>
			toMonthlyInsert(
				{
					fitrahUangIncome: row.fitrahUangIncome,
					fitrahBerasIncome: row.fitrahBerasIncome,
					maalUangIncome: row.maalUangIncome,
					fidyahUangIncome: row.fidyahUangIncome,
					fidyahBerasIncome: row.fidyahBerasIncome,
					infaqUangIncome: row.infaqUangIncome,
					shodaqohUangIncome: row.shodaqohUangIncome,
					fitrahUangOutcome: row.fitrahUangOutcome,
					fitrahBerasOutcome: row.fitrahBerasOutcome,
					maalUangOutcome: row.maalUangOutcome,
					fidyahUangOutcome: row.fidyahUangOutcome,
					fidyahBerasOutcome: row.fidyahBerasOutcome,
					infaqUangOutcome: row.infaqUangOutcome,
					shodaqohUangOutcome: row.shodaqohUangOutcome
				},
				row.day
			)
		);

		if (monthlyRows.length > 0) {
			await db.insert(laporanMonthly).values(monthlyRows);
		}
	})();

	await backfillPromise;
	backfillPromise = null;
}
