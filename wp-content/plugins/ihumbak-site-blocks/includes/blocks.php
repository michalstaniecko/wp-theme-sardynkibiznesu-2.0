<?php
/**
 * Registration of the header blocks, their shared scripts and the `badges` style.
 *
 * One editor script (no build, wp.element.createElement) serves all blocks; one
 * view script module holds the shared Interactivity store `ihumbak/site-header`.
 * Dependencies live in hand-written *.asset.php files next to the scripts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_SITE_BLOCKS = array( 'site-header', 'search-toggle', 'search-bar', 'share-buttons', 'author-social-links' );

add_action( 'init', 'ihumbak_site_blocks_register' );

function ihumbak_site_blocks_register(): void {
	$dir = plugin_dir_path( IHUMBAK_SITE_BLOCKS_FILE );

	$editor = require $dir . 'assets/editor.asset.php';
	wp_register_script(
		'ihumbak-site-blocks-editor',
		plugins_url( 'assets/editor.js', IHUMBAK_SITE_BLOCKS_FILE ),
		$editor['dependencies'],
		$editor['version'],
		array( 'in_footer' => true )
	);
	wp_set_script_translations( 'ihumbak-site-blocks-editor', 'ihumbak-site-blocks' );

	$view = require $dir . 'assets/view.asset.php';
	wp_register_script_module(
		'ihumbak-site-blocks-view',
		plugins_url( 'assets/view.js', IHUMBAK_SITE_BLOCKS_FILE ),
		$view['dependencies'],
		$view['version']
	);

	foreach ( IHUMBAK_SITE_BLOCKS as $block ) {
		register_block_type( $dir . 'blocks/' . $block );
	}

	// Popular search terms as pills; CSS lives in the theme (spec #36, layer 2).
	register_block_style(
		'core/buttons',
		array(
			'name'  => 'badges',
			'label' => __( 'Plakietki', 'ihumbak-site-blocks' ),
		)
	);
}
