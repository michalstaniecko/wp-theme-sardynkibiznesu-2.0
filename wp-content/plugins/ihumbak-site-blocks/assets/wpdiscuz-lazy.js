/**
 * Loads the wpDiscuz assets printed inert by includes/wpdiscuz-lazy.php (#70) when the
 * reader approaches the comments, focuses into them or arrives on a link to them.
 * CSS first and scripts one by one, the order a normal page load would give them.
 */
( function () {
	const section = document.getElementById( 'comments' );
	const scripts = document.getElementById( 'ihumbak-wpdiscuz-scripts' );
	const styles = document.getElementById( 'ihumbak-wpdiscuz-styles' );
	const critical = document.getElementById( 'ihumbak-wpdiscuz-critical-inline-css' );

	if ( ! section || ! scripts || ! styles ) {
		return;
	}

	let started = false;
	let readerMoved = false;

	function hashTarget() {
		try {
			return document.getElementById( decodeURIComponent( location.hash.slice( 1 ) ) );
		} catch ( e ) {
			return null;
		}
	}

	/** `#comment-N` may be on a later page of comments: wpDiscuz fetches it by itself. */
	function hashPointsToComments() {
		const target = hashTarget();
		return (
			/^#comment-\d+$/.test( location.hash ) ||
			( !! target && ( section.contains( target ) || target.contains( section ) ) )
		);
	}

	/** The section changes height as the CSS and the editor arrive. */
	function returnToTarget() {
		const target = hashPointsToComments() && hashTarget();
		if ( target && ! readerMoved ) {
			target.scrollIntoView( { behavior: 'instant' } );
		}
	}

	/** Scripts and links parsed inside <template> or innerHTML stay inert, so they are made anew. */
	function fresh( node ) {
		const element = document.createElement( node.localName );
		for ( const { name, value } of node.attributes ) {
			element.setAttribute( name, value );
		}
		element.textContent = node.textContent;
		return element;
	}

	function loadStyles( done ) {
		const parsed = document.createElement( 'template' );
		parsed.innerHTML = styles.textContent;

		const nodes = Array.from( parsed.content.children, fresh );
		let waiting = nodes.filter( ( node ) => 'LINK' === node.tagName ).length;
		const arrived = () => {
			waiting--;
			if ( waiting < 1 ) {
				done();
			}
		};

		nodes.forEach( ( node ) => {
			if ( 'LINK' === node.tagName ) {
				node.onload = node.onerror = arrived;
			}
			document.head.appendChild( node );
		} );

		if ( ! waiting ) {
			done();
		}
	}

	function loadScripts( nodes, done ) {
		const next = nodes.shift();
		if ( ! next ) {
			done();
			return;
		}

		const script = fresh( next );
		if ( script.src ) {
			script.async = false;
			script.onload = script.onerror = () => loadScripts( nodes, done );
			document.body.appendChild( script );
		} else {
			document.body.appendChild( script );
			loadScripts( nodes, done );
		}
	}

	function start() {
		if ( started ) {
			return;
		}
		started = true;

		loadStyles( () => {
			if ( critical ) {
				critical.remove();
			}
			returnToTarget();
			loadScripts( Array.from( scripts.content.children ), returnToTarget );
		} );
	}

	[ 'wheel', 'touchmove', 'keydown' ].forEach( ( type ) => {
		addEventListener( type, () => ( readerMoved = true ), { once: true, passive: true } );
	} );

	section.addEventListener( 'focusin', start );
	addEventListener( 'hashchange', () => {
		readerMoved = false;
		if ( hashPointsToComments() ) {
			start();
		}
	} );

	if ( hashPointsToComments() ) {
		start();
		return;
	}

	/*
	 * A short post has the section near the viewport from the start, and the assets must not
	 * compete with LCP: wait for `load`, unless the reader has already begun to scroll.
	 */
	let watching = false;
	function watch() {
		if ( watching ) {
			return;
		}
		watching = true;

		const observer = new IntersectionObserver(
			( entries ) => {
				if ( entries.some( ( entry ) => entry.isIntersecting ) ) {
					observer.disconnect();
					start();
				}
			},
			{ rootMargin: '2000px 0px' }
		);
		observer.observe( section );
	}

	if ( 'complete' === document.readyState ) {
		watch();
	} else {
		addEventListener( 'load', watch, { once: true } );
		addEventListener( 'scroll', watch, { once: true, passive: true } );
	}
} )();
