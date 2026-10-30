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

add_action( 'init', __NAMESPACE__ . '\\register_styles' );

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
