import { sql } from 'drizzle-orm';
import { db } from '$lib/server/db';

export async function idNotaExists(idNota: number): Promise<boolean> {
	const [row] = await db.execute(sql`
		select (
			exists(select 1 from zakat_fitrah where id_nota_input = ${idNota})
			or exists(select 1 from zakat_maal where id_nota_input = ${idNota})
			or exists(select 1 from fidyah where id_nota_input = ${idNota})
			or exists(select 1 from infaq where id_nota_input = ${idNota})
			or exists(select 1 from shodaqoh where id_nota_input = ${idNota})
			or exists(select 1 from pengeluaran where id_nota_input = ${idNota})
		) as "exists"
	`);

	return Boolean(row?.exists);
}
