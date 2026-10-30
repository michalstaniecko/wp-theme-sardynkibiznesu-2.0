/**
 * Spis treści in the Kolumna boczna (ihumbak/toc, variant "rail"), store `ihumbak/toc`.
 *
 * Enhancement over the server-rendered list of links (includes/rail.php):
 * - scroll-spy: aria-current="location" on the link of the heading being read;
 * - reading progress of the post content (context.progress, a CSS width);
 * - from 1024px the list follows the active link, and context.covering tells the theme
 *   when a later sidebar block passes under the rail;
 * - below 1024px (theme CSS) a bottom bar with the step ("2/6") and the current heading,
 *   whose button opens the list as a sheet; Escape or a link closes it.
 * Hand-written ES module, no build step (spec #36 toolchain).
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

const DESKTOP = '(min-width: 1024px)';

// Height of the fade at the bottom of the list (theme CSS, spacing 6), where a link is hard to read.
const LIST_FADE = 24;

/**
 * A heading counts as reached once its top passes the line where an anchor jump puts it
 * (its scroll-margin-top, i.e. below the sticky header).
 */
function readingLine( heading ) {
	return parseFloat( window.getComputedStyle( heading ).scrollMarginTop ) || 0;
}

/**
 * Scrolls only the list, never the page, and only when the active link left its visible part.
 */
function keepLinkInView( nav, index ) {
	const list = nav.querySelector( ':scope > .ihumbak-toc__list' );
	const link = list?.querySelectorAll( 'a' )[ index ];
	if ( ! link ) {
		return;
	}

	const listRect = list.getBoundingClientRect();
	const linkRect = link.getBoundingClientRect();
	if (
		linkRect.top >= listRect.top &&
		linkRect.bottom <= listRect.bottom - LIST_FADE
	) {
		return;
	}

	list.scrollTo( {
		top:
			list.scrollTop +
			linkRect.top -
			listRect.top -
			( list.clientHeight - linkRect.height ) / 2,
		behavior: window.matchMedia( '(prefers-reduced-motion: reduce)' )
			.matches
			? 'auto'
			: 'smooth',
	} );
}

/**
 * True from the moment the first later block reaches the rail's bottom edge until the last one
 * clears it, so the gaps between blocks do not count.
 */
function isCovering( nav ) {
	const first = nav.nextElementSibling;
	if ( ! first ) {
		return false;
	}
	const { bottom } = nav.getBoundingClientRect();
	return (
		first.getBoundingClientRect().top < bottom &&
		nav.parentElement.lastElementChild.getBoundingClientRect().bottom >
			bottom
	);
}

function update( context, nav ) {
	const headings = context.items
		.map( ( item ) => document.getElementById( item.id ) )
		.filter( Boolean );
	if ( ! headings.length ) {
		return;
	}

	const line = readingLine( headings[ 0 ] ) + 2;

	let active = -1;
	headings.forEach( ( heading, index ) => {
		if ( heading.getBoundingClientRect().top <= line ) {
			active = index;
		}
	} );
	// At the very end of the page the last headings may never reach the line.
	const atEnd =
		window.innerHeight + window.scrollY >=
		document.documentElement.scrollHeight - 2;
	if ( atEnd && active > -1 ) {
		headings.forEach( ( heading, index ) => {
			if ( heading.getBoundingClientRect().top < window.innerHeight ) {
				active = Math.max( active, index );
			}
		} );
	}

	const moved = active !== context.active;
	context.active = active;
	if ( window.matchMedia( DESKTOP ).matches ) {
		context.covering = isCovering( nav );
		if ( moved ) {
			keepLinkInView( nav, active );
		}
	} else {
		context.covering = false;
	}

	const shown = Math.max( active, 0 );
	context.step = `${ context.items[ shown ].section }/${ context.sections }`;
	context.current = context.items[ shown ].text;

	// Progress through the post content (the block that holds the headings).
	const content =
		headings[ 0 ].closest( '.wp-block-post-content, .entry-content' ) ||
		headings[ 0 ].parentElement;
	const rect = content.getBoundingClientRect();
	const span = rect.height - window.innerHeight + line;
	const ratio = span > 0 ? ( line - rect.top ) / span : 1;
	context.progress = `${ Math.round(
		Math.min( 1, Math.max( 0, ratio ) ) * 100
	) }%`;
}

const { state } = store( 'ihumbak/toc', {
	state: {
		enhanced: false,
		get ariaCurrent() {
			const context = getContext();
			const item = context.items[ context.active ];
			const { attributes } = getElement();
			return item && attributes.href === `#${ item.id }`
				? 'location'
				: null;
		},
	},
	actions: {
		toggle() {
			const context = getContext();
			context.open = ! context.open;
		},
		closeOnLink( event ) {
			if ( event.target.closest( 'a' ) ) {
				getContext().open = false;
			}
		},
		closeOnOutside( event ) {
			const context = getContext();
			if ( context.open && ! getElement().ref.contains( event.target ) ) {
				context.open = false;
			}
		},
		closeOnEscape( event ) {
			const context = getContext();
			if ( event.key !== 'Escape' || ! context.open ) {
				return;
			}
			context.open = false;
			getElement()
				.ref.querySelector( '.ihumbak-toc__toggle' )
				?.focus();
		},
	},
	callbacks: {
		init() {
			state.enhanced = true;
			update( getContext(), getElement().ref );
		},
		spy() {
			update( getContext(), getElement().ref );
		},
	},
} );
