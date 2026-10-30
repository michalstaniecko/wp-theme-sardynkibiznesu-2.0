<?php
/**
 * Sardynki Biznesu 3.0 — presentation only (ADR-0001).
 *
 * Loads the global stylesheet, preloads the two most used font weights and
 * tunes image attributes (sizes, lazy loading) of template images.
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

/**
 * `sizes` of the row card image (.sb-card--row): full width minus root padding
 * below 768px (image above text), the 240px column from 768px up.
 */
const CARD_ROW_IMAGE_SIZES = '(max-width: 767px) calc(100vw - 32px), 240px';

/**
 * Lists (/blog/, category, archives): only the first two card images can be
 * above the fold, so the rest load lazily (core's default leaves three eager).
 */
const LISTING_EAGER_IMAGES = 2;

add_filter(
	'render_block_core/post-featured-image',
	static function ( string $html, array $block ): string {
		$classes = $block['attrs']['className'] ?? '';
		if ( ! str_contains( $classes, 'sb-card__media--row' ) ) {
			return $html;
		}
		$img = new \WP_HTML_Tag_Processor( $html );
		if ( ! $img->next_tag( 'img' ) || ! $img->get_attribute( 'srcset' ) ) {
			return $html;
		}
		// Keep core's `auto` keyword (auto-sizes for lazy images) when present.
		$auto = str_starts_with( (string) $img->get_attribute( 'sizes' ), 'auto,' ) ? 'auto, ' : '';
		$img->set_attribute( 'sizes', $auto . CARD_ROW_IMAGE_SIZES );
		return $img->get_updated_html();
	},
	10,
	2
);

add_filter(
	'wp_omit_loading_attr_threshold',
	static fn ( int $threshold ): int => ( is_home() || is_archive() ) ? LISTING_EAGER_IMAGES : $threshold
);

/**
 * Ramka autora on top of the author archive (.sb-author__avatar) is above the
 * fold, so its avatar must not be lazy (core's get_avatar() makes it lazy).
 */
add_filter(
	'render_block_core/avatar',
	static function ( string $html, array $block ): string {
		if ( ! str_contains( $block['attrs']['className'] ?? '', 'sb-author__avatar' ) ) {
			return $html;
		}
		$img = new \WP_HTML_Tag_Processor( $html );
		if ( $img->next_tag( 'img' ) && 'lazy' === $img->get_attribute( 'loading' ) ) {
			$img->remove_attribute( 'loading' );
		}
		return $img->get_updated_html();
	},
	10,
	2
);
