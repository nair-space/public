import {
	pgTable,
	bigint,
	text,
	integer,
	numeric,
	timestamp,
	date,
	index,
	uniqueIndex,
	pgSequence
} from 'drizzle-orm/pg-core';
import { sql } from 'drizzle-orm';

export * from './auth.schema';

// ──────────────────────────────────────────────
// Shared sequence for unique id_nota_input across all INCOME tables
// ──────────────────────────────────────────────
export const notaSequence = pgSequence('nota_sequence', {
	startWith: 1,
	increment: 1,
	minValue: 1,
	cache: 1
});

// ──────────────────────────────────────────────
// Separate sequence for PENGELUARAN (expense) table
// ──────────────────────────────────────────────
export const notaPengeluaranSequence = pgSequence('nota_pengeluaran_sequence', {
	startWith: 1,
	increment: 1,
	minValue: 1,
	cache: 1
});

// ──────────────────────────────────────────────
// Zakat Fitrah (Income — Uang & Beras)
// ──────────────────────────────────────────────
export const zakatFitrah = pgTable(
	'zakat_fitrah',
	{
		id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
		idNotaInput: bigint('id_nota_input', { mode: 'number' })
			.notNull()
			.unique()
			.default(sql`nextval('nota_sequence')`),
		namaMuzakki: text('nama_muzakki').notNull(),
		jumlahTanggungan: integer('jumlah_tanggungan').default(1),
		zakatFitrahUangIncome: bigint('zakat_fitrah_uang_income', { mode: 'number' })
			.notNull()
			.default(0),
		zakatFitrahBerasIncome: numeric('zakat_fitrah_beras_income', {
			precision: 10,
			scale: 2
		}).default('0'),
		createdAt: timestamp('created_at').defaultNow().notNull(),
		userId: text('user_id').notNull()
	},
	(table) => [
		uniqueIndex('zakat_fitrah_nota_idx').on(table.idNotaInput),
		index('zakat_fitrah_created_at_idx').on(table.createdAt)
	]
);

// ──────────────────────────────────────────────
// Zakat Maal (Income — Uang only)
// ──────────────────────────────────────────────
export const zakatMaal = pgTable(
	'zakat_maal',
	{
		id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
		idNotaInput: bigint('id_nota_input', { mode: 'number' })
			.notNull()
			.unique()
			.default(sql`nextval('nota_sequence')`),
		namaMuzakki: text('nama_muzakki').notNull(),
		zakatMaalUangIncome: bigint('zakat_maal_uang_income', { mode: 'number' })
			.notNull()
			.default(0),
		createdAt: timestamp('created_at').defaultNow().notNull(),
		userId: text('user_id').notNull()
	},
	(table) => [
		uniqueIndex('zakat_maal_nota_idx').on(table.idNotaInput),
		index('zakat_maal_created_at_idx').on(table.createdAt)
	]
);

// ──────────────────────────────────────────────
// Fidyah (Income — Uang & Beras)
// ──────────────────────────────────────────────
export const fidyah = pgTable(
	'fidyah',
	{
		id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
		idNotaInput: bigint('id_nota_input', { mode: 'number' })
			.notNull()
			.unique()
			.default(sql`nextval('nota_sequence')`),
		nama: text('nama').notNull(),
		fidyahUangIncome: bigint('fidyah_uang_income', { mode: 'number' }).notNull().default(0),
		fidyahBerasIncome: numeric('fidyah_beras_income', { precision: 10, scale: 2 }).default('0'),
		createdAt: timestamp('created_at').defaultNow().notNull(),
		userId: text('user_id').notNull()
	},
	(table) => [
		uniqueIndex('fidyah_nota_idx').on(table.idNotaInput),
		index('fidyah_created_at_idx').on(table.createdAt)
	]
);

// ──────────────────────────────────────────────
// Infaq (Income — Uang only)
// ──────────────────────────────────────────────
export const infaq = pgTable(
	'infaq',
	{
		id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
		idNotaInput: bigint('id_nota_input', { mode: 'number' })
			.notNull()
			.unique()
			.default(sql`nextval('nota_sequence')`),
		nama: text('nama').notNull(),
		infaqUangIncome: bigint('infaq_uang_income', { mode: 'number' }).notNull().default(0),
		createdAt: timestamp('created_at').defaultNow().notNull(),
		userId: text('user_id').notNull()
	},
	(table) => [
		uniqueIndex('infaq_nota_idx').on(table.idNotaInput),
		index('infaq_created_at_idx').on(table.createdAt)
	]
);

// ──────────────────────────────────────────────
// Shodaqoh (Income — Uang only)
// ──────────────────────────────────────────────
export const shodaqoh = pgTable(
	'shodaqoh',
	{
		id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
		idNotaInput: bigint('id_nota_input', { mode: 'number' })
			.notNull()
			.unique()
			.default(sql`nextval('nota_sequence')`),
		nama: text('nama').notNull(),
		shodaqohUangIncome: bigint('shodaqoh_uang_income', { mode: 'number' }).notNull().default(0),
		createdAt: timestamp('created_at').defaultNow().notNull(),
		userId: text('user_id').notNull()
	},
	(table) => [
		uniqueIndex('shodaqoh_nota_idx').on(table.idNotaInput),
		index('shodaqoh_created_at_idx').on(table.createdAt)
	]
);

// ──────────────────────────────────────────────
// Pengeluaran (Outcome — all categories)
// ──────────────────────────────────────────────
export const pengeluaran = pgTable(
	'pengeluaran',
	{
		id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
		idNotaInput: bigint('id_nota_input', { mode: 'number' })
			.notNull()
			.unique()
			.default(sql`nextval('nota_pengeluaran_sequence')`),
		namaPengeluaran: text('nama_pengeluaran').notNull(),
		jumlahPengeluaran: bigint('jumlah_pengeluaran', { mode: 'number' }).default(0),
		jumlahPengeluaranBeras: numeric('jumlah_pengeluaran_beras', { precision: 10, scale: 2 }).default('0'),
		zakatFitrahUangOutcome: bigint('zakat_fitrah_uang_outcome', { mode: 'number' }).default(0),
		zakatFitrahBerasOutcome: numeric('zakat_fitrah_beras_outcome', {
			precision: 10,
			scale: 2
		}).default('0'),
		zakatMaalUangOutcome: bigint('zakat_maal_uang_outcome', { mode: 'number' }).default(0),
		fidyahUangOutcome: bigint('fidyah_uang_outcome', { mode: 'number' }).default(0),
		fidyahBerasOutcome: numeric('fidyah_beras_outcome', { precision: 10, scale: 2 }).default('0'),
		infaqUangOutcome: bigint('infaq_uang_outcome', { mode: 'number' }).default(0),
		shodaqohUangOutcome: bigint('shodaqoh_uang_outcome', { mode: 'number' }).default(0),
		createdAt: timestamp('created_at').defaultNow().notNull(),
		userId: text('user_id').notNull()
	},
	(table) => [
		uniqueIndex('pengeluaran_nota_idx').on(table.idNotaInput),
		index('pengeluaran_created_at_idx').on(table.createdAt)
	]
);

// ──────────────────────────────────────────────
// Amil Settings (Global)
// ──────────────────────────────────────────────
export const amilSettings = pgTable('amil_settings', {
	id: integer('id').primaryKey().default(1), // Single row (id=1)
	feeDasar: bigint('fee_dasar', { mode: 'number' }).notNull().default(0),
	jatahAmil: bigint('jatah_amil', { mode: 'number' }).notNull().default(0),
	updatedAt: timestamp('updated_at').defaultNow().notNull()
});

// ──────────────────────────────────────────────
// Amil & Volunteer
// fee_total_amil = absen_amil × fee_dasar (from global settings)
// ──────────────────────────────────────────────
export const amil = pgTable(
	'amil',
	{
		id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
		namaAmil: text('nama_amil').notNull(),
		absenAmil: integer('absen_amil').notNull().default(1), // 1-5
		feeTotalAmil: bigint('fee_total_amil', { mode: 'number' }).notNull().default(0), // calculated: absen × global fee_dasar
		createdAt: timestamp('created_at').defaultNow().notNull(),
		userId: text('user_id').notNull()
	},
	(table) => [index('amil_created_at_idx').on(table.createdAt)]
);

// Summary - Daily
export const laporanDaily = pgTable('laporan_daily', {
	day: date('day').primaryKey(),
	fitrahUangIncome: bigint('fitrah_uang_income', { mode: 'number' }).notNull().default(0),
	fitrahBerasIncome: numeric('fitrah_beras_income', { precision: 10, scale: 2 }).default('0'),
	maalUangIncome: bigint('maal_uang_income', { mode: 'number' }).notNull().default(0),
	fidyahUangIncome: bigint('fidyah_uang_income', { mode: 'number' }).notNull().default(0),
	fidyahBerasIncome: numeric('fidyah_beras_income', { precision: 10, scale: 2 }).default('0'),
	infaqUangIncome: bigint('infaq_uang_income', { mode: 'number' }).notNull().default(0),
	shodaqohUangIncome: bigint('shodaqoh_uang_income', { mode: 'number' }).notNull().default(0),
	fitrahUangOutcome: bigint('fitrah_uang_outcome', { mode: 'number' }).notNull().default(0),
	fitrahBerasOutcome: numeric('fitrah_beras_outcome', { precision: 10, scale: 2 }).default('0'),
	maalUangOutcome: bigint('maal_uang_outcome', { mode: 'number' }).notNull().default(0),
	fidyahUangOutcome: bigint('fidyah_uang_outcome', { mode: 'number' }).notNull().default(0),
	fidyahBerasOutcome: numeric('fidyah_beras_outcome', { precision: 10, scale: 2 }).default('0'),
	infaqUangOutcome: bigint('infaq_uang_outcome', { mode: 'number' }).notNull().default(0),
	shodaqohUangOutcome: bigint('shodaqoh_uang_outcome', { mode: 'number' }).notNull().default(0)
});

// Summary - Monthly
export const laporanMonthly = pgTable('laporan_monthly', {
	month: date('month').primaryKey(),
	fitrahUangIncome: bigint('fitrah_uang_income', { mode: 'number' }).notNull().default(0),
	fitrahBerasIncome: numeric('fitrah_beras_income', { precision: 10, scale: 2 }).default('0'),
	maalUangIncome: bigint('maal_uang_income', { mode: 'number' }).notNull().default(0),
	fidyahUangIncome: bigint('fidyah_uang_income', { mode: 'number' }).notNull().default(0),
	fidyahBerasIncome: numeric('fidyah_beras_income', { precision: 10, scale: 2 }).default('0'),
	infaqUangIncome: bigint('infaq_uang_income', { mode: 'number' }).notNull().default(0),
	shodaqohUangIncome: bigint('shodaqoh_uang_income', { mode: 'number' }).notNull().default(0),
	fitrahUangOutcome: bigint('fitrah_uang_outcome', { mode: 'number' }).notNull().default(0),
	fitrahBerasOutcome: numeric('fitrah_beras_outcome', { precision: 10, scale: 2 }).default('0'),
	maalUangOutcome: bigint('maal_uang_outcome', { mode: 'number' }).notNull().default(0),
	fidyahUangOutcome: bigint('fidyah_uang_outcome', { mode: 'number' }).notNull().default(0),
	fidyahBerasOutcome: numeric('fidyah_beras_outcome', { precision: 10, scale: 2 }).default('0'),
	infaqUangOutcome: bigint('infaq_uang_outcome', { mode: 'number' }).notNull().default(0),
	shodaqohUangOutcome: bigint('shodaqoh_uang_outcome', { mode: 'number' }).notNull().default(0)
});

// Export Jobs
export const exportJobs = pgTable('export_jobs', {
	id: integer('id').primaryKey().generatedAlwaysAsIdentity(),
	status: text('status').notNull(),
	format: text('format').notNull(),
	filename: text('filename'),
	mimeType: text('mime_type'),
	contentBase64: text('content_base64'),
	error: text('error'),
	createdAt: timestamp('created_at').defaultNow().notNull(),
	completedAt: timestamp('completed_at'),
	userId: text('user_id').notNull()
});

