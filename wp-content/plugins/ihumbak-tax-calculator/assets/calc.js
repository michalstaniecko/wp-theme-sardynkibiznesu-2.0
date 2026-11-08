/**
 * Kalkulator VAT: pure calculation module (no DOM, no globals, no side effects on import).
 *
 * Amounts are integer grosze. Rounding is half up and done on integers only, so
 * netto + VAT === brutto for every amount and rate. The largest amount
 * (999 999 999 999,99 zł) keeps every intermediate value below
 * Number.MAX_SAFE_INTEGER, so the arithmetic is exact.
 *
 * Importable from node by path: `import { parseAmount } from '…/calc.js'`.
 */

const MAX_GROSZE = 99_999_999_999_999;
const MAX_INT_DIGITS = 12;
const RATES = [ 23, 8, 5 ];
const MODES = [ 'netto', 'brutto' ];

const pln = new Intl.NumberFormat( 'pl-PL', { style: 'currency', currency: 'PLN' } );

/**
 * Parses a user-typed amount ("1 000,50", "1000.50") into grosze.
 *
 * @param {string} input
 * @return {{ok: true, grosze: number}|{ok: false, error: 'empty'|'format'|'decimals'|'zero'|'too_large'}}
 */
export function parseAmount( input ) {
	let s = String( input ).trim();
	if ( '' === s ) {
		return fail( 'empty' );
	}

	// Thousands separators: space, no-break space, narrow no-break space.
	s = s.replace( /[   ]/g, '' );

	if ( s.includes( ',' ) && s.includes( '.' ) ) {
		return fail( 'format' );
	}

	// A dot is always the decimal separator, never a thousands separator.
	const match = /^(\d+)(?:[.,](\d+))?$/.exec( s );
	if ( ! match ) {
		return fail( 'format' );
	}

	const [ , intPart, fracPart = '' ] = match;
	if ( fracPart.length > 2 ) {
		return fail( 'decimals' );
	}
	if ( intPart.replace( /^0+/, '' ).length > MAX_INT_DIGITS ) {
		return fail( 'too_large' );
	}

	const grosze = Number( intPart ) * 100 + Number( fracPart.padEnd( 2, '0' ) );
	if ( 0 === grosze ) {
		return fail( 'zero' );
	}
	if ( grosze > MAX_GROSZE ) {
		return fail( 'too_large' );
	}

	return { ok: true, grosze };
}

/**
 * Splits an amount into netto, VAT and brutto (all in grosze).
 *
 * mode 'netto': the amount is netto, VAT is computed from it.
 * mode 'brutto': the amount is brutto, VAT is computed from it ("od stu" / "w stu").
 *
 * @param {{grosze: number, mode: 'netto'|'brutto', rate: 23|8|5}} args
 * @return {{netto: number, vat: number, brutto: number}}
 */
export function computeVat( { grosze, mode, rate } ) {
	if ( ! RATES.includes( rate ) ) {
		throw new RangeError( `Unsupported VAT rate: ${ rate }` );
	}
	if ( ! MODES.includes( mode ) ) {
		throw new RangeError( `Unsupported mode: ${ mode }` );
	}
	if ( ! Number.isSafeInteger( grosze ) || grosze < 0 || grosze > MAX_GROSZE ) {
		throw new RangeError( `Amount out of range: ${ grosze }` );
	}

	if ( 'netto' === mode ) {
		const vat = roundDiv( grosze * rate, 100 );
		return { netto: grosze, vat, brutto: grosze + vat };
	}

	const vat = roundDiv( grosze * rate, 100 + rate );
	return { netto: grosze - vat, vat, brutto: grosze };
}

/**
 * Formats grosze as Polish złoty, e.g. "12 345,67 zł".
 *
 * @param {number} grosze
 * @return {string}
 */
export function formatPln( grosze ) {
	return pln.format( grosze / 100 );
}

/** round_half_up( a / d ) for a ≥ 0, d > 0, on integers only. */
function roundDiv( a, d ) {
	return Math.floor( ( 2 * a + d ) / ( 2 * d ) );
}

function fail( error ) {
	return { ok: false, error };
}
