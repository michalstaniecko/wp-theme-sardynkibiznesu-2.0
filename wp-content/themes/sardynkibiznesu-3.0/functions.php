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

/**
 * Per-block CSS: `assets/css/blocks/{namespace}-{block}.css` (e.g. `core-button.css`)
 * loads only on pages that render that block, in the front end and the editor.
 */
add_action(
	'init',
	static function (): void {
		foreach ( glob( get_theme_file_path( 'assets/css/blocks/*.css' ) ) as $path ) {
			$file  = basename( $path, '.css' );
			$block = preg_replace( '/-/', '/', $file, 1 );
			wp_enqueue_block_style(
				$block,
				array(
					'handle' => "sardynkibiznesu-3-0-{$file}",
					'src'    => get_theme_file_uri( "assets/css/blocks/{$file}.css" ),
					'path'   => $path,
					'ver'    => wp_get_theme()->get( 'Version' ),
				)
			);
		}
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
