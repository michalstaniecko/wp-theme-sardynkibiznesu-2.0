<?php
/**
 * Sardynki Biznesu 3.0 — presentation only (ADR-0001).
 *
 * Loads the global stylesheet, preloads the two most used font weights,
 * tunes image attributes (sizes, lazy loading) of template images, formats
 * the result count of the search view, maps the 2.0 narrow page template, builds the
 * Karta produktu markup of patterns and registers the pattern category of the Sekcje
 * strony głównej. Registers no shortcodes, blocks, post types, meta or settings.
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
 * `sizes` of the article's and page's featured image: the image never exceeds the 680 content
 * column (>= 1024px), below that it spans the viewport minus the root padding
 * (24px per side from 768px, 16px below). Core would say "up to 1200px wide".
 */
const ARTICLE_FEATURED_IMAGE_SIZES = '(min-width: 1024px) 680px, (min-width: 768px) calc(100vw - 48px), calc(100vw - 32px)';

add_filter(
	'render_block_core/post-featured-image',
	static function ( string $html, array $block ): string {
		$class_name = $block['attrs']['className'] ?? '';
		// The front page hero and featured podcast images are ~680px wide too (7/12 of the 1200 column).
		if ( ! array_intersect( array( 'sb-article__featured', 'sb-page__featured', 'sb-hero__media', 'sb-podcast__media' ), explode( ' ', $class_name ) ) ) {
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
 * `sizes` of the row card image (.sb-card--row): full width minus root padding
 * below 768px (image above text), the 240px column from 768px up.
 */
const CARD_ROW_IMAGE_SIZES = '(max-width: 767px) calc(100vw - 32px), 240px';

/**
 * `sizes` of the column card image (.sb-card--col, front page grids): a third of the
 * 1200 column (minus two 24px gaps), a third of the viewport from 768px, full width below.
 */
const CARD_COL_IMAGE_SIZES = '(min-width: 1248px) 384px, (min-width: 768px) calc((100vw - 96px) / 3), calc(100vw - 32px)';

/**
 * Lists (/blog/, category, archives, search): only the first two card images can be
 * above the fold, so the rest load lazily (core's default leaves three eager).
 */
const LISTING_EAGER_IMAGES = 2;

add_filter(
	'render_block_core/post-featured-image',
	static function ( string $html, array $block ): string {
		$classes = explode( ' ', $block['attrs']['className'] ?? '' );
		if ( in_array( 'sb-card__media--row', $classes, true ) ) {
			$sizes = CARD_ROW_IMAGE_SIZES;
		} elseif ( in_array( 'sb-card__media--col', $classes, true ) ) {
			$sizes = CARD_COL_IMAGE_SIZES;
		} else {
			return $html;
		}
		$img = new \WP_HTML_Tag_Processor( $html );
		if ( ! $img->next_tag( 'img' ) || ! $img->get_attribute( 'srcset' ) ) {
			return $html;
		}
		// Keep core's `auto` keyword (auto-sizes for lazy images) when present.
		$auto = str_starts_with( (string) $img->get_attribute( 'sizes' ), 'auto,' ) ? 'auto, ' : '';
		$img->set_attribute( 'sizes', $auto . $sizes );
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
 * 2.0 page template meta → 3.0 block template. The 18 pages saved with the 2.0 template
 * „Boxed without sidebar” render with `page-narrow`; their `_wp_page_template` meta stays
 * as it is, so 2.0 keeps working on the same database (#20).
 */
const LEGACY_PAGE_TEMPLATES = array(
	'templates/boxed-without-sidebar.php' => 'page-narrow.php',
);

add_filter(
	'page_template_hierarchy',
	static function ( array $templates ): array {
		return array_values(
			array_unique(
				array_map( static fn ( string $template ): string => LEGACY_PAGE_TEMPLATES[ $template ] ?? $template, $templates )
			)
		);
	}
);

/**
 * Block markup of a grid of Karty produktu (core blocks only, spec #36), shared by the
 * patterns product-cards-* and the Sekcje strony głównej „Polecamy na start” / „Darmowe
 * narzędzia”. The card CSS (core-group--product.css) selects children by position, so
 * every card has the same structure: [image,] badge, body (category, H3, text, button).
 *
 * @param array<int, array{label: string, label_style: string, category: string, title: string, desc: string, button: string, outline?: bool, url?: string, category_url?: string, image?: string}> $cards
 * @param string $variant `horizontal` (image, 2 columns) or `compact` (no image, 3 columns).
 */
function product_cards_markup( array $cards, string $variant ): string {
	$horizontal = 'horizontal' === $variant;
	$grid       = $horizontal
		? '{"type":"grid","columnCount":2,"minimumColumnWidth":"22rem"}'
		: '{"type":"grid","columnCount":3,"minimumColumnWidth":"16rem"}';
	$style      = $horizontal ? 'product-horizontal' : 'product-compact';
	$placeholder = get_theme_file_uri( 'assets/images/product-placeholder.svg' );

	$out = "<!-- wp:group {\"layout\":$grid} -->\n<div class=\"wp-block-group\">\n";
	foreach ( $cards as $card ) {
		$url      = esc_url( $card['url'] ?? '#' );
		$cat_url  = esc_url( $card['category_url'] ?? ( $card['url'] ?? '#' ) );
		$label    = esc_attr( $card['label_style'] );
		$button   = ! empty( $card['outline'] )
			? "\t\t\t\t<!-- wp:button {\"className\":\"is-style-outline\"} -->\n\t\t\t\t<div class=\"wp-block-button is-style-outline\">"
			: "\t\t\t\t<!-- wp:button -->\n\t\t\t\t<div class=\"wp-block-button\">";
		$out .= "\t<!-- wp:group {\"className\":\"is-style-$style\",\"layout\":{\"type\":\"default\"}} -->\n\t<div class=\"wp-block-group is-style-$style\">\n";
		if ( $horizontal ) {
			$out .= "\t\t<!-- wp:image {\"sizeSlug\":\"large\",\"linkDestination\":\"none\"} -->\n"
				. "\t\t<figure class=\"wp-block-image size-large\"><img src=\"" . esc_url( $card['image'] ?? $placeholder ) . "\" alt=\"\"/></figure>\n"
				. "\t\t<!-- /wp:image -->\n\n";
		}
		$out .= "\t\t<!-- wp:paragraph {\"className\":\"is-style-$label\"} -->\n\t\t<p class=\"is-style-$label\">" . esc_html( $card['label'] ) . "</p>\n\t\t<!-- /wp:paragraph -->\n\n"
			. "\t\t<!-- wp:group {\"layout\":{\"type\":\"default\"}} -->\n\t\t<div class=\"wp-block-group\">\n"
			. "\t\t\t<!-- wp:paragraph -->\n\t\t\t<p><a href=\"$cat_url\">" . esc_html( $card['category'] ) . "</a></p>\n\t\t\t<!-- /wp:paragraph -->\n\n"
			. "\t\t\t<!-- wp:heading {\"level\":3} -->\n\t\t\t<h3 class=\"wp-block-heading\"><a href=\"$url\">" . esc_html( $card['title'] ) . "</a></h3>\n\t\t\t<!-- /wp:heading -->\n\n"
			. "\t\t\t<!-- wp:paragraph -->\n\t\t\t<p>" . esc_html( $card['desc'] ) . "</p>\n\t\t\t<!-- /wp:paragraph -->\n\n"
			. "\t\t\t<!-- wp:buttons -->\n\t\t\t<div class=\"wp-block-buttons\">\n"
			. $button . "<a class=\"wp-block-button__link wp-element-button\" href=\"$url\">" . esc_html( $card['button'] ) . "</a></div>\n\t\t\t\t<!-- /wp:button -->\n"
			. "\t\t\t</div>\n\t\t\t<!-- /wp:buttons -->\n"
			. "\t\t</div>\n\t\t<!-- /wp:group -->\n"
			. "\t</div>\n\t<!-- /wp:group -->\n";
	}
	return $out . "</div>\n<!-- /wp:group -->\n";
}

/** Permalink of a published page by path, or the plain URL of that path when the page is missing. */
function page_url( string $path ): string {
	$page = get_page_by_path( $path );
	return $page && 'publish' === $page->post_status ? (string) get_permalink( $page ) : home_url( '/' . trim( $path, '/' ) . '/' );
}

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
