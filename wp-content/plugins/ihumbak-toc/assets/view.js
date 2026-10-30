/**
 * Spis treści in the Kolumna boczna (ihumbak/toc, variant "rail"), store `ihumbak/toc`.
 *
 * Enhancement over the server-rendered list of links (includes/rail.php):
 * - scroll-spy: aria-current="location" on the link of the heading being read;
 * - reading progress of the post content (context.progress, a CSS width);
 * - below 1024px (theme CSS) a bottom bar with the step ("2/6") and the current heading,
 *   whose button opens the list as a sheet; Escape or a link closes it.
 * Hand-written ES module, no build step (spec #36 toolchain).
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

/**
 * A heading counts as reached once its top passes the line where an anchor jump puts it
 * (its scroll-margin-top, i.e. below the sticky header).
 */
function readingLine( heading ) {
	return parseFloat( window.getComputedStyle( heading ).scrollMarginTop ) || 0;
}

function update( context ) {
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

	context.active = active;
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
			update( getContext() );
		},
		spy() {
			update( getContext() );
		},
	},
} );
