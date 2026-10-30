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

/**
 * `sizes` of the article's featured image: the image never exceeds the 680 content
 * column (>= 1024px), below that it spans the viewport minus the root padding
 * (24px per side from 768px, 16px below). Core would say "up to 1200px wide".
 */
const ARTICLE_FEATURED_IMAGE_SIZES = '(min-width: 1024px) 680px, (min-width: 768px) calc(100vw - 48px), calc(100vw - 32px)';

add_filter(
	'render_block_core/post-featured-image',
	static function ( string $html, array $block ): string {
		$class_name = $block['attrs']['className'] ?? '';
		if ( ! in_array( 'sb-article__featured', explode( ' ', $class_name ), true ) ) {
			return $html;
		}
		$tags = new \WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag( 'img' ) && null !== $tags->get_attribute( 'srcset' ) ) {
			$tags->set_attribute( 'sizes', ARTICLE_FEATURED_IMAGE_SIZES );
		}
		return $tags->get_updated_html();
	},
	10,
	2
);

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
