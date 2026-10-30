<?php
/**
 * Karta produktu: a pattern made of core blocks, no block of its own and no
 * prices. The card is a `core/group` with one of two block styles registered here;
 * the patterns and the CSS live in the theme (`patterns/product-cards-*.php`,
 * `assets/css/blocks/core-group--product.css`).
 *
 * Card structure the CSS relies on: image (`core/image`, optional), label
 * (`core/paragraph` with a Plakietka style), body (`core/group`: category paragraph,
 * title heading, description paragraph) and `core/buttons`. `cards_markup()` builds that
 * markup for the theme's patterns (ADR-0001: the theme only calls it, guarded by
 * `function_exists()`), `page_url()` resolves the card links.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\ProductCard;

defined( 'ABSPATH' ) || exit;

/** Karta produktu variants of `core/group`: slug => label. */
const STYLES = array(
	'product-horizontal' => 'Karta pozioma',
	'product-compact'    => 'Karta kompaktowa',
);

add_action( 'init', __NAMESPACE__ . '\\register_styles' );

/**
 * Registers the Karta produktu variants as block styles of `core/group`.
 */
function register_styles(): void {
	foreach ( STYLES as $name => $label ) {
		register_block_style(
			'core/group',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}

/**
 * Block markup of a grid of Karty produktu (core blocks only, spec #36), shared by the theme
 * patterns product-cards-* and the Sekcje strony głównej „Polecamy na start” / „Darmowe
 * narzędzia”. The card CSS (core-group--product.css) selects children by position, so
 * every card has the same structure: [image,] badge, body (category, H3, text, button).
 *
 * @param array<int, array{label: string, label_style: string, category: string, title: string, desc: string, button: string, outline?: bool, url?: string, category_url?: string, image?: string}> $cards
 * @param string $variant           `horizontal` (image, 2 columns) or `compact` (no image, 3 columns).
 * @param string $placeholder_image Image URL of a horizontal card without its own `image` (the theme's placeholder).
 */
function cards_markup( array $cards, string $variant, string $placeholder_image = '' ): string {
	$horizontal = 'horizontal' === $variant;
	$grid       = $horizontal
		? '{"type":"grid","columnCount":2,"minimumColumnWidth":"22rem"}'
		: '{"type":"grid","columnCount":3,"minimumColumnWidth":"16rem"}';
	$style      = $horizontal ? 'product-horizontal' : 'product-compact';

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
				. "\t\t<figure class=\"wp-block-image size-large\"><img src=\"" . esc_url( $card['image'] ?? $placeholder_image ) . "\" alt=\"\"/></figure>\n"
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
