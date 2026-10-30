<?php
/**
 * Sardynki Biznesu 3.0 — presentation only (ADR-0001).
 *
 * Loads the global stylesheet and preloads the two most used font weights.
 * Registers no shortcodes, blocks, post types, meta or settings.
 *
 * @package sardynkibiznesu-3.0
 */

namespace SardynkiBiznesu\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Font weights preloaded in <head>: 400 (body copy) and 700 (H1, the usual LCP text).
 * The remaining weights (500, 600) load on demand via @font-face from theme.json.
 */
const PRELOADED_FONT_WEIGHTS = array( 400, 700 );

add_action(
	'after_setup_theme',
	static function (): void {
		add_editor_style( 'style.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$theme = wp_get_theme();
		wp_enqueue_style(
			'sardynkibiznesu-3-0-style',
			get_stylesheet_uri(),
			array( 'global-styles' ),
			$theme->get( 'Version' )
		);
	}
);

/**
 * Per-block styles (layer 3 of spec #36): block name => file in assets/css/.
 * Loaded only when the block is on the page, also in the editor. The blocks
 * themselves come from ihumbak-* plugins.
 */
const BLOCK_STYLES = array(
	'ihumbak/site-header' => 'header',
	'ihumbak/search-bar'  => 'search-bar',
	'core/buttons'        => 'buttons-badges',
);

add_action(
	'init',
	static function (): void {
		$version = wp_get_theme()->get( 'Version' );
		foreach ( BLOCK_STYLES as $block => $file ) {
			wp_enqueue_block_style(
				$block,
				array(
					'handle' => "sardynkibiznesu-3-0-{$file}",
					'src'    => get_theme_file_uri( "assets/css/{$file}.css" ),
					'path'   => get_theme_file_path( "assets/css/{$file}.css" ),
					'ver'    => $version,
				)
			);
		}
	}
);

add_action(
	'wp_head',
	static function (): void {
		foreach ( PRELOADED_FONT_WEIGHTS as $weight ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( get_theme_file_uri( "assets/fonts/montserrat-{$weight}.woff2" ) )
			);
		}
	},
	1
);
