CREATE TABLE "amil_settings" (
	"id" integer PRIMARY KEY DEFAULT 1 NOT NULL,
	"fee_dasar" bigint DEFAULT 0 NOT NULL,
	"jatah_amil" bigint DEFAULT 0 NOT NULL,
	"updated_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "export_jobs" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "export_jobs_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"status" text NOT NULL,
	"format" text NOT NULL,
	"filename" text,
	"mime_type" text,
	"content_base64" text,
	"error" text,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"completed_at" timestamp,
	"user_id" text NOT NULL
);
--> statement-breakpoint
CREATE TABLE "laporan_daily" (
	"day" date PRIMARY KEY NOT NULL,
	"fitrah_uang_income" bigint DEFAULT 0 NOT NULL,
	"fitrah_beras_income" numeric(10, 2) DEFAULT '0',
	"maal_uang_income" bigint DEFAULT 0 NOT NULL,
	"fidyah_uang_income" bigint DEFAULT 0 NOT NULL,
	"fidyah_beras_income" numeric(10, 2) DEFAULT '0',
	"infaq_uang_income" bigint DEFAULT 0 NOT NULL,
	"shodaqoh_uang_income" bigint DEFAULT 0 NOT NULL,
	"fitrah_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"fitrah_beras_outcome" numeric(10, 2) DEFAULT '0',
	"maal_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"fidyah_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"fidyah_beras_outcome" numeric(10, 2) DEFAULT '0',
	"infaq_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"shodaqoh_uang_outcome" bigint DEFAULT 0 NOT NULL
);
--> statement-breakpoint
CREATE TABLE "laporan_monthly" (
	"month" date PRIMARY KEY NOT NULL,
	"fitrah_uang_income" bigint DEFAULT 0 NOT NULL,
	"fitrah_beras_income" numeric(10, 2) DEFAULT '0',
	"maal_uang_income" bigint DEFAULT 0 NOT NULL,
	"fidyah_uang_income" bigint DEFAULT 0 NOT NULL,
	"fidyah_beras_income" numeric(10, 2) DEFAULT '0',
	"infaq_uang_income" bigint DEFAULT 0 NOT NULL,
	"shodaqoh_uang_income" bigint DEFAULT 0 NOT NULL,
	"fitrah_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"fitrah_beras_outcome" numeric(10, 2) DEFAULT '0',
	"maal_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"fidyah_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"fidyah_beras_outcome" numeric(10, 2) DEFAULT '0',
	"infaq_uang_outcome" bigint DEFAULT 0 NOT NULL,
	"shodaqoh_uang_outcome" bigint DEFAULT 0 NOT NULL
);
--> statement-breakpoint
CREATE INDEX "amil_created_at_idx" ON "amil" USING btree ("created_at");--> statement-breakpoint
CREATE INDEX "fidyah_created_at_idx" ON "fidyah" USING btree ("created_at");--> statement-breakpoint
CREATE INDEX "infaq_created_at_idx" ON "infaq" USING btree ("created_at");--> statement-breakpoint
CREATE INDEX "pengeluaran_created_at_idx" ON "pengeluaran" USING btree ("created_at");--> statement-breakpoint
CREATE INDEX "shodaqoh_created_at_idx" ON "shodaqoh" USING btree ("created_at");--> statement-breakpoint
CREATE INDEX "zakat_fitrah_created_at_idx" ON "zakat_fitrah" USING btree ("created_at");--> statement-breakpoint
CREATE INDEX "zakat_maal_created_at_idx" ON "zakat_maal" USING btree ("created_at");
