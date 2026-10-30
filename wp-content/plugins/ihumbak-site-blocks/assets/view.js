/**
 * Shared Interactivity store of the header blocks (ihumbak/site-header,
 * ihumbak/search-toggle, ihumbak/search-bar): `scrolled` state of the frame
 * and the open/closed search bar.
 */
import { store, getElement, withSyncEvent } from '@wordpress/interactivity';

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
