/**
 * Currency formatting utilities for Indonesian Rupiah.
 * Currency is stored as BIGINT (e.g., 4000000 = Rp.4.000.000).
 */

/**
 * Format a number (BIGINT representation) to Indonesian Rupiah string.
 * @example formatRupiah(4000000) → "Rp.4.000.000"
 */
export function formatRupiah(amount: number | bigint | null | undefined): string {
	if (amount === null || amount === undefined) return 'Rp.0';

	const num = typeof amount === 'bigint' ? Number(amount) : amount;
	const formatted = Math.abs(num)
		.toString()
		.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

	return num < 0 ? `-Rp.${formatted}` : `Rp.${formatted}`;
}

/**
 * Parse a Rupiah string back to a number.
 * @example parseRupiah("Rp.4.000.000") → 4000000
 */
export function parseRupiah(str: string): number {
	if (!str) return 0;
	const cleaned = str.replace(/[Rp.\s]/g, '');
	return parseInt(cleaned, 10) || 0;
}

/**
 * Format weight in kg with 2 decimal places.
 * @example formatWeight(2.5) → "2,50 kg"
 */
export function formatWeight(kg: number | null | undefined): string {
	if (kg === null || kg === undefined) return '0,00 kg';
	return kg.toFixed(2).replace('.', ',') + ' kg';
}
