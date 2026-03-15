CREATE SEQUENCE "public"."nota_sequence" INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START WITH 1 CACHE 1;--> statement-breakpoint
CREATE TABLE "amil" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "amil_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"nama_amil" text NOT NULL,
	"absen_amil" integer DEFAULT 1 NOT NULL,
	"fee_dasar_amil" bigint DEFAULT 0 NOT NULL,
	"fee_total_amil" bigint DEFAULT 0 NOT NULL,
	"jatah_amil" bigint DEFAULT 0,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"user_id" text NOT NULL
);
--> statement-breakpoint
CREATE TABLE "fidyah" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "fidyah_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"id_nota_input" bigint DEFAULT nextval('nota_sequence') NOT NULL,
	"nama" text NOT NULL,
	"fidyah_uang_income" bigint DEFAULT 0 NOT NULL,
	"fidyah_beras_income" numeric(10, 2) DEFAULT '0',
	"created_at" timestamp DEFAULT now() NOT NULL,
	"user_id" text NOT NULL,
	CONSTRAINT "fidyah_id_nota_input_unique" UNIQUE("id_nota_input")
);
--> statement-breakpoint
CREATE TABLE "infaq" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "infaq_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"id_nota_input" bigint DEFAULT nextval('nota_sequence') NOT NULL,
	"nama" text NOT NULL,
	"infaq_uang_income" bigint DEFAULT 0 NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"user_id" text NOT NULL,
	CONSTRAINT "infaq_id_nota_input_unique" UNIQUE("id_nota_input")
);
--> statement-breakpoint
CREATE TABLE "pengeluaran" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "pengeluaran_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"id_nota_input" bigint DEFAULT nextval('nota_sequence') NOT NULL,
	"nama_pengeluaran" text NOT NULL,
	"zakat_fitrah_uang_outcome" bigint DEFAULT 0,
	"zakat_fitrah_beras_outcome" numeric(10, 2) DEFAULT '0',
	"zakat_maal_uang_outcome" bigint DEFAULT 0,
	"fidyah_uang_outcome" bigint DEFAULT 0,
	"fidyah_beras_outcome" numeric(10, 2) DEFAULT '0',
	"infaq_uang_outcome" bigint DEFAULT 0,
	"shodaqoh_uang_outcome" bigint DEFAULT 0,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"user_id" text NOT NULL,
	CONSTRAINT "pengeluaran_id_nota_input_unique" UNIQUE("id_nota_input")
);
--> statement-breakpoint
CREATE TABLE "shodaqoh" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "shodaqoh_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"id_nota_input" bigint DEFAULT nextval('nota_sequence') NOT NULL,
	"nama" text NOT NULL,
	"shodaqoh_uang_income" bigint DEFAULT 0 NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"user_id" text NOT NULL,
	CONSTRAINT "shodaqoh_id_nota_input_unique" UNIQUE("id_nota_input")
);
--> statement-breakpoint
CREATE TABLE "zakat_fitrah" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "zakat_fitrah_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"id_nota_input" bigint DEFAULT nextval('nota_sequence') NOT NULL,
	"nama_muzakki" text NOT NULL,
	"jumlah_tanggungan" integer DEFAULT 1,
	"zakat_fitrah_uang_income" bigint DEFAULT 0 NOT NULL,
	"zakat_fitrah_beras_income" numeric(10, 2) DEFAULT '0',
	"created_at" timestamp DEFAULT now() NOT NULL,
	"user_id" text NOT NULL,
	CONSTRAINT "zakat_fitrah_id_nota_input_unique" UNIQUE("id_nota_input")
);
--> statement-breakpoint
CREATE TABLE "zakat_maal" (
	"id" integer PRIMARY KEY GENERATED ALWAYS AS IDENTITY (sequence name "zakat_maal_id_seq" INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 START WITH 1 CACHE 1),
	"id_nota_input" bigint DEFAULT nextval('nota_sequence') NOT NULL,
	"nama_muzakki" text NOT NULL,
	"zakat_maal_uang_income" bigint DEFAULT 0 NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"user_id" text NOT NULL,
	CONSTRAINT "zakat_maal_id_nota_input_unique" UNIQUE("id_nota_input")
);
--> statement-breakpoint
CREATE TABLE "account" (
	"id" text PRIMARY KEY NOT NULL,
	"account_id" text NOT NULL,
	"provider_id" text NOT NULL,
	"user_id" text NOT NULL,
	"access_token" text,
	"refresh_token" text,
	"id_token" text,
	"access_token_expires_at" timestamp,
	"refresh_token_expires_at" timestamp,
	"scope" text,
	"password" text,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"updated_at" timestamp NOT NULL
);
--> statement-breakpoint
CREATE TABLE "session" (
	"id" text PRIMARY KEY NOT NULL,
	"expires_at" timestamp NOT NULL,
	"token" text NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"updated_at" timestamp NOT NULL,
	"ip_address" text,
	"user_agent" text,
	"user_id" text NOT NULL,
	CONSTRAINT "session_token_unique" UNIQUE("token")
);
--> statement-breakpoint
CREATE TABLE "user" (
	"id" text PRIMARY KEY NOT NULL,
	"name" text NOT NULL,
	"email" text NOT NULL,
	"email_verified" boolean DEFAULT false NOT NULL,
	"image" text,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"updated_at" timestamp DEFAULT now() NOT NULL,
	CONSTRAINT "user_email_unique" UNIQUE("email")
);
--> statement-breakpoint
CREATE TABLE "verification" (
	"id" text PRIMARY KEY NOT NULL,
	"identifier" text NOT NULL,
	"value" text NOT NULL,
	"expires_at" timestamp NOT NULL,
	"created_at" timestamp DEFAULT now() NOT NULL,
	"updated_at" timestamp DEFAULT now() NOT NULL
);
--> statement-breakpoint
ALTER TABLE "account" ADD CONSTRAINT "account_user_id_user_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."user"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "session" ADD CONSTRAINT "session_user_id_user_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."user"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
CREATE UNIQUE INDEX "fidyah_nota_idx" ON "fidyah" USING btree ("id_nota_input");--> statement-breakpoint
CREATE UNIQUE INDEX "infaq_nota_idx" ON "infaq" USING btree ("id_nota_input");--> statement-breakpoint
CREATE UNIQUE INDEX "pengeluaran_nota_idx" ON "pengeluaran" USING btree ("id_nota_input");--> statement-breakpoint
CREATE UNIQUE INDEX "shodaqoh_nota_idx" ON "shodaqoh" USING btree ("id_nota_input");--> statement-breakpoint
CREATE UNIQUE INDEX "zakat_fitrah_nota_idx" ON "zakat_fitrah" USING btree ("id_nota_input");--> statement-breakpoint
CREATE UNIQUE INDEX "zakat_maal_nota_idx" ON "zakat_maal" USING btree ("id_nota_input");--> statement-breakpoint
CREATE INDEX "account_userId_idx" ON "account" USING btree ("user_id");--> statement-breakpoint
CREATE INDEX "session_userId_idx" ON "session" USING btree ("user_id");--> statement-breakpoint
CREATE INDEX "verification_identifier_idx" ON "verification" USING btree ("identifier");