<?php
/**
 * Przycisk (GLOSSARY): `core/button` with block styles as variants.
 *
 * New content uses `core/button` and picks a variant by block style. The three legacy
 * notations — `[sb_button]`, `[cmsmasters_button]` and the ACF block `acf/sb-button` —
 * render the very same `core/buttons` > `core/button` markup, so one CSS (in the theme)
 * styles them all. The database is never changed.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\Button;

defined( 'ABSPATH' ) || exit;

/** Variant styles of `core/button`: slug => label. `outline` overrides the core label. */
const STYLES = array(
	'outline'  => 'Obrys',
	'accent'   => 'Akcent',
	'download' => 'Ikona pobierania',
);

/** Label of legacy buttons saved without one (as in 2.0). */
const DEFAULT_LABEL = 'Sprawdź zawartość kursu';

add_action( 'init', __NAMESPACE__ . '\\register_styles' );
add_shortcode( 'sb_button', __NAMESPACE__ . '\\sb_button_shortcode' );

/**
 * Registers the Przycisk variants as block styles of `core/button`.
 */
function register_styles(): void {
	foreach ( STYLES as $name => $label ) {
		register_block_style(
			'core/button',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}

/**
 * Renders one Przycisk exactly as the block editor saves `core/buttons` > `core/button`
 * (`wp.blocks.serialize`), then runs it through the block renderer, so the output, the
 * layout classes and the enqueued core CSS are the same as for a native button.
 *
 * @param string $text    Button text, already escaped HTML.
 * @param string $url     Link target (escaped here).
 * @param string $style   Block style slug from STYLES, or '' for the default (fill).
 * @param string $justify Justification of `core/buttons`: left|center|right, or '' for none.
 * @param string $target  `target` attribute, e.g. '_blank', or ''.
 * @param string $rel     `rel` attribute, or ''.
 */
function render( string $text, string $url, string $style = '', string $justify = '', string $target = '', string $rel = '' ): string {
	$buttons_attrs = '';
	if ( in_array( $justify, array( 'left', 'center', 'right' ), true ) ) {
		$buttons_attrs = wp_json_encode(
			array(
				'layout' => array(
					'type'           => 'flex',
					'justifyContent' => $justify,
				),
			)
		) . ' ';
	}

	$class        = isset( STYLES[ $style ] ) ? 'is-style-' . $style : '';
	$button_attrs = $class ? wp_json_encode( array( 'className' => $class ) ) . ' ' : '';

	// esc_url() would write `&` as `&#038;`; the editor saves `&amp;`.
	$link = '<a class="wp-block-button__link wp-element-button" href="' . esc_attr( esc_url_raw( $url ) ) . '"';
	if ( '' !== $target ) {
		$link .= ' target="' . esc_attr( $target ) . '"';
	}
	if ( '' !== $rel ) {
		$link .= ' rel="' . esc_attr( $rel ) . '"';
	}
	$link .= '>' . $text . '</a>';

	$markup = '<!-- wp:buttons ' . $buttons_attrs . "-->\n"
		. '<div class="wp-block-buttons"><!-- wp:button ' . $button_attrs . "-->\n"
		. '<div class="' . esc_attr( trim( 'wp-block-button ' . $class ) ) . '">' . $link . "</div>\n"
		. "<!-- /wp:button --></div>\n"
		. '<!-- /wp:buttons -->';

	return do_blocks( $markup );
}

/**
 * `[sb_button url="…" label="…" rel="…" /]` → Przycisk with the outline style,
 * opened in a new tab, `rel="nofollow"` by default (as in 2.0).
 *
 * @param array|string $atts Shortcode attributes.
 */
function sb_button_shortcode( $atts ): string {
	$a = shortcode_atts(
		array(
			'url'   => '#',
			'label' => DEFAULT_LABEL,
			'rel'   => 'nofollow',
		),
		$atts,
		'sb_button'
	);

	$label = '' !== trim( (string) $a['label'] ) ? (string) $a['label'] : DEFAULT_LABEL;

	return render( esc_html( $label ), (string) $a['url'], 'outline', '', '_blank', (string) $a['rel'] );
}
