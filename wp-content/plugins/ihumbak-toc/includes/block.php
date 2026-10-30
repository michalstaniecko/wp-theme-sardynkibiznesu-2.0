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

// Keeps the Kotwice written on save in the open editor (assets/editor.js).
add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		wp_enqueue_script(
			'ihumbak-toc-editor',
			plugins_url( 'assets/editor.js', __DIR__ ),
			array( 'wp-blocks', 'wp-data', 'wp-editor', 'wp-block-editor' ),
			(string) filemtime( dirname( __DIR__ ) . '/assets/editor.js' ),
			array( 'in_footer' => true )
		);
	}
);
