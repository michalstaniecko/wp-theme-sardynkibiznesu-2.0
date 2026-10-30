<?php
/**
 * Sardynki Biznesu 3.0 — presentation layer (ADR-0001).
 *
 * Loads the global and per-block stylesheets, preloads the two most used font weights,
 * tunes image attributes (sizes, lazy loading, fetch priority) of template images, formats
 * the result count of the search view and registers the pattern category of the Sekcje
 * strony głównej. Everything that must survive a theme change (content compatibility with
 * 2.0, Karta produktu markup, blocks, shortcodes, meta, settings) lives in the ihumbak-*
 * plugins; patterns call plugin functions only behind `function_exists()`.
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
 * A `--{part}` suffix splits one block's CSS by component, e.g. `core-group--product.css`
 * (Karta produktu) next to `core-group.css` (Notka, Ramka promocyjna).
 */
add_action(
	'init',
	static function (): void {
		foreach ( glob( get_theme_file_path( 'assets/css/blocks/*.css' ) ) as $path ) {
			$file  = basename( $path, '.css' );
			$block = preg_replace( '/-/', '/', explode( '--', $file, 2 )[0], 1 );
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
 * `sizes` of a ~680px wide image: the article's and page's featured image never exceeds the 680
 * content column (>= 1024px), below that it spans the viewport minus the root padding (24px per
 * side from 768px, 16px below). Core would say "up to 1200px wide". The front page hero and the
 * featured podcast image are ~680px wide too (7/12 of the 1200 column).
 */
const WIDE_IMAGE_SIZES = '(min-width: 1024px) 680px, (min-width: 768px) calc(100vw - 48px), calc(100vw - 32px)';

/**
 * `sizes` of the row card image (.sb-card--row): full width minus root padding
 * below 768px (image above text), the 240px column from 768px up.
 */
const CARD_ROW_IMAGE_SIZES = '(max-width: 767px) calc(100vw - 32px), 240px';

/**
 * `sizes` of the column card image (.sb-card--col, front page grids): a third of the
 * 1200 column (minus two 24px gaps), a third of the viewport from 768px, full width below.
 */
const CARD_COL_IMAGE_SIZES = '(min-width: 1248px) 384px, (min-width: 768px) calc((100vw - 96px) / 3), calc(100vw - 32px)';

/** className of a core/post-featured-image => `sizes` of its image. */
const FEATURED_IMAGE_SIZES = array(
	'sb-article__featured' => WIDE_IMAGE_SIZES,
	'sb-page__featured'    => WIDE_IMAGE_SIZES,
	'sb-hero__media'       => WIDE_IMAGE_SIZES,
	'sb-podcast__media'    => WIDE_IMAGE_SIZES,
	'sb-card__media--row'  => CARD_ROW_IMAGE_SIZES,
	'sb-card__media--col'  => CARD_COL_IMAGE_SIZES,
);

/**
 * Featured images that are always above the fold, the LCP candidates: the article's image (#47)
 * and the front page hero (#58). They get fetchpriority="high" and are never lazy, whatever
 * core's position heuristics decide.
 */
const ABOVE_THE_FOLD_IMAGES = array( 'sb-article__featured', 'sb-hero__media' );

/**
 * Lists (/blog/, category, archives, search): only the first two card images can be
 * above the fold, so the rest load lazily (core's default leaves three eager).
 */
const LISTING_EAGER_IMAGES = 2;

add_filter(
	'render_block_core/post-featured-image',
	static function ( string $html, array $block ): string {
		$classes = explode( ' ', $block['attrs']['className'] ?? '' );
		$known   = array_values( array_intersect( $classes, array_keys( FEATURED_IMAGE_SIZES ) ) );
		if ( ! $known ) {
			return $html;
		}
		$img = new \WP_HTML_Tag_Processor( $html );
		if ( ! $img->next_tag( 'img' ) ) {
			return $html;
		}
		if ( array_intersect( ABOVE_THE_FOLD_IMAGES, $classes ) ) {
			// Core skips counting an image that already says fetchpriority="high"; count it here, so the
			// images after it get the same eager/lazy split as when core marked it itself.
			if ( 'high' !== $img->get_attribute( 'fetchpriority' ) ) {
				wp_increase_content_media_count();
			}
			$img->remove_attribute( 'loading' );
			$img->set_attribute( 'fetchpriority', 'high' );
		}
		if ( $img->get_attribute( 'srcset' ) ) {
			// Keep core's `auto` keyword (auto-sizes, valid on lazy images only) when present.
			$lazy = 'lazy' === $img->get_attribute( 'loading' );
			$auto = $lazy && str_starts_with( (string) $img->get_attribute( 'sizes' ), 'auto,' ) ? 'auto, ' : '';
			$img->set_attribute( 'sizes', $auto . FEATURED_IMAGE_SIZES[ $known[0] ] );
		}
		return $img->get_updated_html();
	},
	10,
	2
);

add_filter(
	'wp_omit_loading_attr_threshold',
	static fn ( int $threshold ): int => ( is_home() || is_archive() || is_search() ) ? LISTING_EAGER_IMAGES : $threshold
);

/**
 * Result count line of the search view: "<strong>38 wyników</strong> dla „rodo”",
 * "<strong>Brak wyników</strong> dla „rodo”"; without „dla …” for an empty query.
 * Square brackets are encoded too, so a query can never become a shortcode.
 */
function search_summary_html( int $found, string $query ): string {
	if ( 0 === $found ) {
		$count = 'Brak wyników';
	} else {
		$tens = $found % 100;
		$ones = $found % 10;
		if ( 1 === $found ) {
			$noun = 'wynik';
		} elseif ( $ones >= 2 && $ones <= 4 && ( $tens < 12 || $tens > 14 ) ) {
			$noun = 'wyniki';
		} else {
			$noun = 'wyników';
		}
		$count = number_format_i18n( $found ) . ' ' . $noun;
	}

	$html  = '<strong>' . esc_html( $count ) . '</strong>';
	$query = trim( $query );
	if ( '' !== $query ) {
		$html .= ' dla „' . str_replace( array( '[', ']' ), array( '&#91;', '&#93;' ), esc_html( $query ) ) . '”';
	}
	return $html;
}

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

/**
 * Sekcje strony głównej (#58): slugs of the section patterns `home-{slug}` in their default
 * order. Pattern `home` (offered for a new page) concatenates them into the front page content.
 */
const HOME_SECTIONS = array( 'hero', 'latest', 'start', 'podcast', 'newsletter', 'most-read', 'topics', 'tools' );

add_action(
	'init',
	static function (): void {
		register_block_pattern_category(
			'sardynkibiznesu-3-0-home',
			array(
				'label'       => 'Sekcje strony głównej',
				'description' => 'Samodzielne sekcje strony głównej: Hero, Najnowsze, Polecamy na start, Podcast, Newsletter, Najczęściej czytane, O czym piszemy, Darmowe narzędzia.',
			)
		);
	},
	9
);
