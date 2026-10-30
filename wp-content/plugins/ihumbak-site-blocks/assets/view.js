/**
 * Shared Interactivity store of the header blocks (ihumbak/site-header,
 * ihumbak/search-toggle, ihumbak/search-bar): `scrolled` state of the frame
 * and the open/closed search bar.
 */
import { store, getContext, getElement, withSyncEvent } from '@wordpress/interactivity';

const TOGGLE_SELECTOR = 'button[aria-controls="sb-search-bar"]';

const { state } = store( 'ihumbak/site-header', {
	state: {
		get headerState() {
			return state.isScrolled ? 'scrolled' : 'top';
		},
	},
	actions: {
		toggleSearch() {
			state.isSearchOpen = ! state.isSearchOpen;
		},
		closeOnEscape( event ) {
			if ( event.key !== 'Escape' || ! state.isSearchOpen ) {
				return;
			}
			state.isSearchOpen = false;
			document.querySelector( TOGGLE_SELECTOR )?.focus();
		},
		// Overlay accordion toggles (see includes/navigation-overlay.php).
		keepFocusOnPress: withSyncEvent( ( event ) => {
			event.preventDefault();
		} ),
	},
	callbacks: {
		hydrate() {
			// With JS the search link becomes the toggle button.
			state.isHydrated = true;
		},
		syncScrolled() {
			state.isScrolled = window.scrollY > 0;
		},
		focusSearch() {
			if ( ! state.isSearchOpen ) {
				return;
			}
			const { ref } = getElement();
			// `hidden` is removed in the same tick; focus after paint.
			window.requestAnimationFrame( () =>
				ref.querySelector( 'input[type="search"]' )?.focus()
			);
		},
	},
} );

/**
 * ihumbak/share-buttons: copy link and Web Share, each shown only when the
 * browser has the API. The e-mail link needs no JS.
 */
const COPIED_MS = 2500;

const { state: shareState } = store( 'ihumbak/share-buttons', {
	state: {
		get statusText() {
			return getContext().copied ? shareState.copiedText : '';
		},
	},
	actions: {
		*copyLink() {
			const context = getContext();
			try {
				yield navigator.clipboard.writeText( context.url );
			} catch {
				return;
			}
			context.copied = true;
			yield new Promise( ( resolve ) => setTimeout( resolve, COPIED_MS ) );
			context.copied = false;
		},
		*share() {
			const { url, title } = getContext();
			try {
				yield navigator.share( { title, url } );
			} catch {
				// AbortError when the reader closes the share sheet: nothing to do.
			}
		},
	},
	callbacks: {
		detect() {
			shareState.canCopy = window.isSecureContext && !! navigator.clipboard?.writeText;
			shareState.canShare = typeof navigator.share === 'function';
		},
	},
} );
