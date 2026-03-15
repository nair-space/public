import type { PageServerLoad } from './$types';
import { db } from '$lib/server/db';
import {
	fidyah,
	infaq,
	laporanMonthly,
	pengeluaran,
	shodaqoh,
	zakatFitrah,
	zakatMaal
} from '$lib/server/db/schema';
import { ensureSummaryBackfill } from '$lib/server/db/summary';
import { desc, sum } from 'drizzle-orm';

const TRANSACTION_LIMIT = 100;

function toNumber(value: unknown): number {
	if (typeof value === 'number') return value;
	if (typeof value === 'string') return Number(value) || 0;
	return 0;
}

type TransactionItem = {
	label: string;
	amount: number;
	createdAt: Date;
	idNotaInput: number | null;
};

function sortByNewest(items: TransactionItem[]): TransactionItem[] {
	return items.sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime());
}

export const load: PageServerLoad = async ({ locals }) => {
	if (!locals.session) {
		return {
			totals: {
				fitrahUang: { income: 0, outcome: 0 },
				fitrahBeras: { income: 0, outcome: 0 },
				maalUang: { income: 0, outcome: 0 },
				fidyahUang: { income: 0, outcome: 0 },
				fidyahBeras: { income: 0, outcome: 0 },
				infaqUang: { income: 0, outcome: 0 },
				shodaqohUang: { income: 0, outcome: 0 }
			},
			transactions: {
				incomeUang: [],
				incomeBeras: [],
				outcomeUang: [],
				outcomeBeras: []
			}
		};
	}

	await ensureSummaryBackfill();

	const [summaryTotals] = await db
		.select({
			fitrahUangIncome: sum(laporanMonthly.fitrahUangIncome),
			fitrahBerasIncome: sum(laporanMonthly.fitrahBerasIncome),
			maalUangIncome: sum(laporanMonthly.maalUangIncome),
			fidyahUangIncome: sum(laporanMonthly.fidyahUangIncome),
			fidyahBerasIncome: sum(laporanMonthly.fidyahBerasIncome),
			infaqUangIncome: sum(laporanMonthly.infaqUangIncome),
			shodaqohUangIncome: sum(laporanMonthly.shodaqohUangIncome),
			fitrahUangOutcome: sum(laporanMonthly.fitrahUangOutcome),
			fitrahBerasOutcome: sum(laporanMonthly.fitrahBerasOutcome),
			maalUangOutcome: sum(laporanMonthly.maalUangOutcome),
			fidyahUangOutcome: sum(laporanMonthly.fidyahUangOutcome),
			fidyahBerasOutcome: sum(laporanMonthly.fidyahBerasOutcome),
			infaqUangOutcome: sum(laporanMonthly.infaqUangOutcome),
			shodaqohUangOutcome: sum(laporanMonthly.shodaqohUangOutcome)
		})
		.from(laporanMonthly);

	const transactionsPromise = Promise.all([
		db
			.select()
			.from(zakatFitrah)
			.orderBy(desc(zakatFitrah.createdAt))
			.limit(TRANSACTION_LIMIT),
		db
			.select()
			.from(zakatMaal)
			.orderBy(desc(zakatMaal.createdAt))
			.limit(TRANSACTION_LIMIT),
		db
			.select()
			.from(fidyah)
			.orderBy(desc(fidyah.createdAt))
			.limit(TRANSACTION_LIMIT),
		db
			.select()
			.from(infaq)
			.orderBy(desc(infaq.createdAt))
			.limit(TRANSACTION_LIMIT),
		db
			.select()
			.from(shodaqoh)
			.orderBy(desc(shodaqoh.createdAt))
			.limit(TRANSACTION_LIMIT),
		db
			.select()
			.from(pengeluaran)
			.orderBy(desc(pengeluaran.createdAt))
			.limit(TRANSACTION_LIMIT)
	]).then(
		([zakatFitrahList, zakatMaalList, fidyahList, infaqList, shodaqohList, pengeluaranList]) => {
			const incomeUang = sortByNewest([
				...zakatFitrahList
					.filter((row) => toNumber(row.zakatFitrahUangIncome) > 0)
					.map((row) => ({
						label: 'Zakat Fitrah (Uang)',
						amount: toNumber(row.zakatFitrahUangIncome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...zakatMaalList
					.filter((row) => toNumber(row.zakatMaalUangIncome) > 0)
					.map((row) => ({
						label: 'Zakat Maal',
						amount: toNumber(row.zakatMaalUangIncome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...fidyahList
					.filter((row) => toNumber(row.fidyahUangIncome) > 0)
					.map((row) => ({
						label: 'Fidyah (Uang)',
						amount: toNumber(row.fidyahUangIncome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...infaqList
					.filter((row) => toNumber(row.infaqUangIncome) > 0)
					.map((row) => ({
						label: 'Infaq',
						amount: toNumber(row.infaqUangIncome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...shodaqohList
					.filter((row) => toNumber(row.shodaqohUangIncome) > 0)
					.map((row) => ({
						label: 'Shodaqoh',
						amount: toNumber(row.shodaqohUangIncome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					}))
			]);

			const incomeBeras = sortByNewest([
				...zakatFitrahList
					.filter((row) => toNumber(row.zakatFitrahBerasIncome) > 0)
					.map((row) => ({
						label: 'Zakat Fitrah (Beras)',
						amount: toNumber(row.zakatFitrahBerasIncome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...fidyahList
					.filter((row) => toNumber(row.fidyahBerasIncome) > 0)
					.map((row) => ({
						label: 'Fidyah (Beras)',
						amount: toNumber(row.fidyahBerasIncome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					}))
			]);

			const outcomeUang = sortByNewest([
				...pengeluaranList
					.filter((row) => toNumber(row.zakatFitrahUangOutcome) > 0)
					.map((row) => ({
						label: 'Zakat Fitrah (Uang) Outcome',
						amount: toNumber(row.zakatFitrahUangOutcome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...pengeluaranList
					.filter((row) => toNumber(row.zakatMaalUangOutcome) > 0)
					.map((row) => ({
						label: 'Zakat Maal (Uang) Outcome',
						amount: toNumber(row.zakatMaalUangOutcome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...pengeluaranList
					.filter((row) => toNumber(row.fidyahUangOutcome) > 0)
					.map((row) => ({
						label: 'Fidyah (Uang) Outcome',
						amount: toNumber(row.fidyahUangOutcome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...pengeluaranList
					.filter((row) => toNumber(row.infaqUangOutcome) > 0)
					.map((row) => ({
						label: 'Infaq (Uang) Outcome',
						amount: toNumber(row.infaqUangOutcome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...pengeluaranList
					.filter((row) => toNumber(row.shodaqohUangOutcome) > 0)
					.map((row) => ({
						label: 'Shodaqoh (Uang) Outcome',
						amount: toNumber(row.shodaqohUangOutcome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					}))
			]);

			const outcomeBeras = sortByNewest([
				...pengeluaranList
					.filter((row) => toNumber(row.zakatFitrahBerasOutcome) > 0)
					.map((row) => ({
						label: 'Zakat Fitrah (Beras) Outcome',
						amount: toNumber(row.zakatFitrahBerasOutcome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					})),
				...pengeluaranList
					.filter((row) => toNumber(row.fidyahBerasOutcome) > 0)
					.map((row) => ({
						label: 'Fidyah (Beras) Outcome',
						amount: toNumber(row.fidyahBerasOutcome),
						createdAt: row.createdAt,
						idNotaInput: row.idNotaInput
					}))
			]);

			return {
				incomeUang,
				incomeBeras,
				outcomeUang,
				outcomeBeras
			};
		}
	);

	const totals = {
		fitrahUang: {
			income: toNumber(summaryTotals?.fitrahUangIncome),
			outcome: toNumber(summaryTotals?.fitrahUangOutcome)
		},
		fitrahBeras: {
			income: toNumber(summaryTotals?.fitrahBerasIncome),
			outcome: toNumber(summaryTotals?.fitrahBerasOutcome)
		},
		maalUang: {
			income: toNumber(summaryTotals?.maalUangIncome),
			outcome: toNumber(summaryTotals?.maalUangOutcome)
		},
		fidyahUang: {
			income: toNumber(summaryTotals?.fidyahUangIncome),
			outcome: toNumber(summaryTotals?.fidyahUangOutcome)
		},
		fidyahBeras: {
			income: toNumber(summaryTotals?.fidyahBerasIncome),
			outcome: toNumber(summaryTotals?.fidyahBerasOutcome)
		},
		infaqUang: {
			income: toNumber(summaryTotals?.infaqUangIncome),
			outcome: toNumber(summaryTotals?.infaqUangOutcome)
		},
		shodaqohUang: {
			income: toNumber(summaryTotals?.shodaqohUangIncome),
			outcome: toNumber(summaryTotals?.shodaqohUangOutcome)
		}
	};

	return {
		totals,
		transactions: transactionsPromise
	};
};
