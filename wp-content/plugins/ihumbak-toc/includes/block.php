<?php
/**
 * The ihumbak/toc block (render: blocks/toc/render.php).
 */

namespace Ihumbak\Toc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Values of the block's "variant" attribute; the <nav> gets the class ihumbak-toc--{variant}.
 * "rail" (Kolumna boczna, scroll-spy) is extended by its own ticket.
 */
const VARIANTS = array( 'inline', 'rail' );

add_action(
	'init',
	static function (): void {
		register_block_type( dirname( __DIR__ ) . '/blocks/toc' );
	}
);
