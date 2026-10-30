/**
 * Formularz zapisu with JavaScript, store `ihumbak/newsletter` (includes/form.php, includes/rest.php).
 *
 * The browser validates the fields first (HTML5 `required`, `type=email`); the submit
 * event only fires for a valid form. Then the form's FormData (signed hidden fields
 * included) goes to the REST endpoint instead of a page load:
 * - 200 → the Strona podziękowania from the response;
 * - 422 → the server's message (invalid e-mail, missing name) in the form's aria-live region;
 * - anything else, or no response → the generic message there. No modal.
 * Without JavaScript the same form posts to admin-post.php.
 * Hand-written ES module, no build step (spec #36 toolchain).
 */
import { store, getContext, withSyncEvent } from '@wordpress/interactivity';

const FIELD_ERRORS = { email: 'invalid_email', name: 'missing_name' };

const invalid = ( field ) => ( getContext().error === FIELD_ERRORS[ field ] ? 'true' : null );
const describedBy = ( field ) => {
	const context = getContext();
	return context.error === FIELD_ERRORS[ field ] ? context.messageId : null;
};

const { state } = store( 'ihumbak/newsletter', {
	state: {
		get emailInvalid() {
			return invalid( 'email' );
		},
		get emailDescribedBy() {
			return describedBy( 'email' );
		},
		get nameInvalid() {
			return invalid( 'name' );
		},
		get nameDescribedBy() {
			return describedBy( 'name' );
		},
	},
	actions: {
		submit: withSyncEvent( function* ( event ) {
			event.preventDefault();
			const context = getContext();
			if ( context.sending ) {
				return;
			}
			const form = event.target;

			context.sending = true;
			// Emptied first, so the same message is announced again on a repeated error.
			context.error = '';
			context.message = '';

			let error = 'error';
			let message = state.genericError;
			try {
				const response = yield fetch( state.endpoint, {
					method: 'POST',
					body: new FormData( form ),
					credentials: 'same-origin',
					headers: { Accept: 'application/json' },
				} );
				const data = yield response.json().catch( () => ( {} ) );
				if ( response.ok && 'ok' === data.code && data.redirect ) {
					window.location.assign( data.redirect );
					return; // Stays "sending" until the page changes.
				}
				if ( 422 === response.status && data.message ) {
					error = data.code;
					message = data.message;
				}
			} catch ( e ) {
				// Network failure: generic message.
			}

			context.sending = false;
			context.error = error;
			context.message = message;
		} ),
		pageshow: withSyncEvent( ( event ) => {
			// Back from the Strona podziękowania (bfcache): the form must be usable again.
			if ( event.persisted ) {
				getContext().sending = false;
			}
		} ),
	},
} );
