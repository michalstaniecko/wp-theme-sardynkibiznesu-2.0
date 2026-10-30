<?php
/**
 * Plakietka: a pill label. Two notations, both block styles of core blocks:
 * - `core/paragraph` with a `badge*` style: one label (Karta produktu's etykieta,
 *   episode number in the Podcast section);
 * - `core/buttons` with the `badges` style: a row of link badges (e.g. "Popularne"
 *   searches in the search bar).
 *
 * The look lives in the theme (`assets/css/blocks/core-paragraph.css`, `core-buttons.css`).
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\Badges;

defined( 'ABSPATH' ) || exit;

/**
 * Plakietka variants of `core/paragraph`: slug => label. Design system: default
 * (turquoise, topic), brand (Bestseller, Nowość, Polecane), success (Za darmo),
 * neutral (content type: Podcast, Wideo).
 */
const PARAGRAPH_STYLES = array(
	'badge'         => 'Plakietka',
	'badge-brand'   => 'Plakietka: wyróżnienie',
	'badge-success' => 'Plakietka: za darmo',
	'badge-neutral' => 'Plakietka: typ treści',
);

add_action( 'init', __NAMESPACE__ . '\\register_styles' );

/**
 * Registers the Plakietka block styles.
 */
function register_styles(): void {
	foreach ( PARAGRAPH_STYLES as $name => $label ) {
		register_block_style(
			'core/paragraph',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}

	register_block_style(
		'core/buttons',
		array(
			'name'  => 'badges',
			'label' => 'Plakietki',
		)
	);
}
