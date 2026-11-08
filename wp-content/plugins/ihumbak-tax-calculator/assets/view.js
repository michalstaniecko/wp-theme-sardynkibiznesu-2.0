/**
 * Kalkulator VAT: form handling for every [data-ihumbak-tax-calculator] on the page.
 *
 * The form never submits: "Oblicz" (or Enter in the amount field) validates the amount
 * with parseAmount() and fills in the results. Errors are shown under the field and
 * cleared live once the value becomes valid ("reward early, punish late"). Changing
 * the mode or rate does not recalculate. Every user-facing text comes from the
 * data-messages JSON rendered by PHP.
 */
import { parseAmount, computeVat, formatPln } from 'ihumbak-tax-calculator-calc';

const ANNOUNCE_DELAY = 100;

/** Replaces %1$s…%n$s in a template with the given values. */
function fill( template, values ) {
	return template.replace( /%(\d+)\$s/g, ( match, n ) => values[ n - 1 ] ?? match );
}

function init( root ) {
	let messages;
	try {
		messages = JSON.parse( root.dataset.messages );
	} catch {
		return;
	}

	const form = root.querySelector( 'form' );
	const input = root.querySelector( '.ihumbak-tax-calculator__input' );
	const error = root.querySelector( '.ihumbak-tax-calculator__error' );
	const hint = root.querySelector( '.ihumbak-tax-calculator__hint' );
	const submit = root.querySelector( '.ihumbak-tax-calculator__submit' );
	const status = root.querySelector( '[role="status"]' );
	const result = ( name ) => root.querySelector( `[data-result="${ name }"]` );
	if ( ! form || ! input || ! error || ! hint || ! submit || ! status ) {
		return;
	}

	let validateLive = false;
	let announceTimer = 0;

	const showError = ( code ) => {
		error.textContent = messages[ code ] ?? messages.format;
		error.hidden = false;
		input.setAttribute( 'aria-invalid', 'true' );
		input.setAttribute( 'aria-describedby', `${ hint.id } ${ error.id }` );
	};

	const clearError = () => {
		error.textContent = '';
		error.hidden = true;
		input.removeAttribute( 'aria-invalid' );
		input.setAttribute( 'aria-describedby', hint.id );
	};

	const checked = ( suffix ) => form.querySelector( `input[type="radio"][name$="-${ suffix }"]:checked` );

	form.noValidate = true;

	form.addEventListener( 'submit', ( event ) => {
		event.preventDefault();

		const parsed = parseAmount( input.value );
		if ( ! parsed.ok ) {
			validateLive = true;
			showError( parsed.error );
			input.focus();
			return;
		}
		clearError();

		const mode = checked( 'mode' )?.value === 'netto' ? 'netto' : 'brutto';
		const rate = Number( checked( 'rate' )?.value ?? 23 );
		const { netto, vat, brutto } = computeVat( { grosze: parsed.grosze, mode, rate } );
		const rateLabel = messages.rates?.[ rate ] ?? String( rate );

		const amounts = { netto: formatPln( netto ), vat: formatPln( vat ), brutto: formatPln( brutto ) };
		for ( const [ name, text ] of Object.entries( amounts ) ) {
			const output = result( name );
			if ( output ) {
				output.textContent = text;
			}
		}
		const rateEl = result( 'rate' );
		if ( rateEl ) {
			rateEl.textContent = rateLabel;
		}

		// Empty first, then the sentence, so the same result is announced again.
		status.textContent = '';
		clearTimeout( announceTimer );
		announceTimer = setTimeout( () => {
			status.textContent = fill( messages.status, [ amounts.netto, rateLabel, amounts.vat, amounts.brutto ] );
		}, ANNOUNCE_DELAY );
	} );

	input.addEventListener( 'input', () => {
		if ( validateLive && parseAmount( input.value ).ok ) {
			clearError();
		}
	} );

	submit.disabled = false;
}

document.querySelectorAll( '[data-ihumbak-tax-calculator]' ).forEach( init );
