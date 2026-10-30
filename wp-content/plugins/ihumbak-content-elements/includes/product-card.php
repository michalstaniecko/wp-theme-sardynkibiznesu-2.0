<?php
/**
 * Karta produktu (GLOSSARY): a pattern made of core blocks, no block of its own and no
 * prices. The card is a `core/group` with one of two block styles registered here;
 * the patterns and the CSS live in the theme (`patterns/product-cards-*.php`,
 * `assets/css/blocks/core-group--product.css`).
 *
 * Card structure the CSS relies on: image (`core/image`, optional), label
 * (`core/paragraph` with a Plakietka style), body (`core/group`: category paragraph,
 * title heading, description paragraph) and `core/buttons`.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\ProductCard;

defined( 'ABSPATH' ) || exit;

/** Karta produktu variants of `core/group`: slug => label. */
const STYLES = array(
	'product-horizontal' => 'Karta produktu: pozioma',
	'product-compact'    => 'Karta produktu: kompaktowa',
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
