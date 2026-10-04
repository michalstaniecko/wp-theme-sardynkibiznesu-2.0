/**
 * iHumbak Compare Table - frontend behaviour.
 *
 * Vanilla JS, no dependencies. Everything is delegated from the document, so
 * any number of tables on a page share one set of listeners.
 *
 * - Sections: the toggle flips `.is-open` / `aria-expanded` and sets `inert`
 *   on a closed body. PHP renders the initial state.
 * - Tooltips: CSS opens them on hover and focus. This file adds tap/click,
 *   Esc, outside click and keeps the bubble inside the viewport (shifted
 *   sideways, flipped above its toggle near the bottom edge).
 */
( function () {
	'use strict';

	const SECTION = '.ihumbak-ct__section';
	const SECTION_TOGGLE = '.ihumbak-ct__section-toggle';
	const VALUE = '.ihumbak-ct__value';
	const TIP = '.ihumbak-ct__tooltip';
	const TIP_TOGGLE = '.ihumbak-ct__tooltip-toggle';

	const OPEN_CLASS = 'is-open';
	const ANIMATING_CLASS = 'is-animating';
	const DISMISSED_CLASS = 'is-tooltip-dismissed';
	const ABOVE_CLASS = 'is-above';

	// Viewport gap kept around a tooltip, in px.
	const EDGE = 8;
	// Slightly longer than the CSS transition; fallback when transitionend is missed.
	const ANIMATION_TIMEOUT = 400;

	const reducedMotion = window.matchMedia
		? window.matchMedia( '(prefers-reduced-motion: reduce)' )
		: { matches: false };

	function closest( target, selector ) {
		return target && target.closest ? target.closest( selector ) : null;
	}

	/* Sections ----------------------------------------------------------- */

	function sectionBody( toggle ) {
		const id = toggle.getAttribute( 'aria-controls' );

		return id ? document.getElementById( id ) : null;
	}

	function setInert( body, inert ) {
		if ( inert ) {
			body.setAttribute( 'inert', '' );
		} else {
			body.removeAttribute( 'inert' );
		}
	}

	function setSection( toggle, open ) {
		const section = closest( toggle, SECTION );
		const body = sectionBody( toggle );

		if ( ! section || ! body ) {
			return;
		}

		if ( ! open ) {
			closeTooltips( section );
		}

		// The body is clipped only while it moves, so tooltips of a settled
		// open section can overflow it.
		if ( ! reducedMotion.matches ) {
			section.classList.add( ANIMATING_CLASS );
			window.clearTimeout( section.ihumbakCtTimer );
			section.ihumbakCtTimer = window.setTimeout( function () {
				section.classList.remove( ANIMATING_CLASS );
			}, ANIMATION_TIMEOUT );
		}

		section.classList.toggle( OPEN_CLASS, open );
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		setInert( body, ! open );
	}

	function initSections() {
		const toggles = document.querySelectorAll( SECTION_TOGGLE );

		Array.prototype.forEach.call( toggles, function ( toggle ) {
			const body = sectionBody( toggle );

			if ( body ) {
				setInert(
					body,
					toggle.getAttribute( 'aria-expanded' ) !== 'true'
				);
			}
		} );
	}

	document.addEventListener( 'transitionend', function ( event ) {
		if (
			event.propertyName === 'grid-template-rows' &&
			event.target.classList &&
			event.target.classList.contains( 'ihumbak-ct__section-body' )
		) {
			const section = closest( event.target, SECTION );

			if ( section ) {
				window.clearTimeout( section.ihumbakCtTimer );
				section.classList.remove( ANIMATING_CLASS );
			}
		}
	} );

	/* Tooltips ----------------------------------------------------------- */

	function tooltipOf( toggle ) {
		const id = toggle.getAttribute( 'aria-describedby' );

		return id ? document.getElementById( id ) : null;
	}

	/**
	 * Keeps the bubble inside the viewport: shifts it horizontally, and puts
	 * it above the toggle when it does not fit below and there is more room
	 * above. Works while the bubble is still hidden: it is laid out either way.
	 * @param {Element} toggle Tooltip toggle button.
	 */
	function clampTooltip( toggle ) {
		const tip = tooltipOf( toggle );

		if ( ! tip ) {
			return;
		}

		// Measured in the default position: below the toggle, not shifted.
		const wrap = tip.parentElement;

		tip.style.setProperty( '--ct-tip-shift', '0px' );
		wrap.classList.remove( ABOVE_CLASS );

		const rect = tip.getBoundingClientRect();
		const anchor = toggle.getBoundingClientRect();
		const roomBelow = document.documentElement.clientHeight - anchor.bottom;

		if ( rect.bottom - anchor.bottom > roomBelow - EDGE ) {
			wrap.classList.toggle( ABOVE_CLASS, anchor.top > roomBelow );
		}

		const viewport = document.documentElement.clientWidth;
		let shift = 0;

		if ( rect.right > viewport - EDGE ) {
			shift = viewport - EDGE - rect.right;
		}

		// The left edge wins when the bubble is wider than the viewport.
		if ( rect.left + shift < EDGE ) {
			shift = EDGE - rect.left;
		}

		tip.style.setProperty( '--ct-tip-shift', Math.round( shift ) + 'px' );
	}

	function setTooltip( toggle, open ) {
		if ( open ) {
			const cell = closest( toggle, VALUE );

			if ( cell ) {
				cell.classList.remove( DISMISSED_CLASS );
			}

			clampTooltip( toggle );
		}

		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	function closeTooltips( root, except ) {
		const open = ( root || document ).querySelectorAll(
			TIP_TOGGLE + '[aria-expanded="true"]'
		);

		Array.prototype.forEach.call( open, function ( toggle ) {
			if ( toggle !== except ) {
				setTooltip( toggle, false );
			}
		} );
	}

	/**
	 * Esc also has to hide a bubble that is open only through CSS (hover or
	 * focus). It stays hidden until the pointer and the focus leave the cell.
	 */
	function dismissCssTooltips() {
		const cells = document.querySelectorAll(
			VALUE + ':hover, ' + VALUE + ':focus-within'
		);

		Array.prototype.forEach.call( cells, function ( cell ) {
			if ( ! cell.querySelector( TIP ) ) {
				return;
			}

			cell.classList.add( DISMISSED_CLASS );

			const restore = function () {
				cell.classList.remove( DISMISSED_CLASS );
				cell.removeEventListener( 'mouseleave', restore );
				cell.removeEventListener( 'focusout', restore );
			};

			cell.addEventListener( 'mouseleave', restore );
			cell.addEventListener( 'focusout', restore );
		} );
	}

	/* Events ------------------------------------------------------------- */

	document.addEventListener( 'click', function ( event ) {
		const sectionToggle = closest( event.target, SECTION_TOGGLE );

		if ( sectionToggle ) {
			setSection(
				sectionToggle,
				sectionToggle.getAttribute( 'aria-expanded' ) !== 'true'
			);
			return;
		}

		const tipToggle = closest( event.target, TIP_TOGGLE );

		if ( tipToggle ) {
			const open = tipToggle.getAttribute( 'aria-expanded' ) !== 'true';

			closeTooltips( document, tipToggle );
			setTooltip( tipToggle, open );
		}
	} );

	// Outside click. pointerdown rather than click: iOS Safari does not send
	// click events for taps on non-interactive elements.
	document.addEventListener(
		'onpointerdown' in window ? 'pointerdown' : 'mousedown',
		function ( event ) {
			if (
				closest( event.target, TIP_TOGGLE ) ||
				closest( event.target, TIP )
			) {
				return;
			}

			closeTooltips( document );
		}
	);

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key !== 'Escape' && event.key !== 'Esc' ) {
			return;
		}

		closeTooltips( document );
		dismissCssTooltips();
	} );

	// Position before CSS shows the bubble on hover or focus.
	document.addEventListener( 'mouseover', function ( event ) {
		const toggle = closest( event.target, TIP_TOGGLE );

		if ( toggle && ! toggle.contains( event.relatedTarget ) ) {
			clampTooltip( toggle );
		}
	} );

	document.addEventListener( 'focusin', function ( event ) {
		const toggle = closest( event.target, TIP_TOGGLE );

		if ( toggle ) {
			clampTooltip( toggle );
		}
	} );

	// Tabbing away closes a pinned bubble.
	document.addEventListener( 'focusout', function ( event ) {
		const toggle = closest( event.target, TIP_TOGGLE );

		if ( ! toggle || toggle.getAttribute( 'aria-expanded' ) !== 'true' ) {
			return;
		}

		const cell = closest( toggle, VALUE );

		if (
			event.relatedTarget &&
			cell &&
			! cell.contains( event.relatedTarget )
		) {
			setTooltip( toggle, false );
		}
	} );

	window.addEventListener( 'resize', function () {
		const open = document.querySelectorAll(
			TIP_TOGGLE + '[aria-expanded="true"]'
		);

		Array.prototype.forEach.call( open, clampTooltip );
	} );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initSections );
	} else {
		initSections();
	}
} )();
