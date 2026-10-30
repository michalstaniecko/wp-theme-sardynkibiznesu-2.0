<?php
/**
 * Notka and Ramka promocyjna (GLOSSARY): `core/group` with block styles.
 *
 * The editor inserts a group and picks a style: `neutral` is the Notka (a warning or
 * remark, any blocks inside: paragraphs, lists), `accent` and `inverse` are the two
 * variants of the Ramka promocyjna. The look lives in the theme (ADR-0001).
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\GroupStyles;

defined( 'ABSPATH' ) || exit;

/** Block styles of `core/group`: slug => label. */
const STYLES = array(
	'neutral' => 'Notka',
	'accent'  => 'Ramka promocyjna: akcent',
	'inverse' => 'Ramka promocyjna: odwrócona',
);

add_action( 'init', __NAMESPACE__ . '\\register_styles' );

/**
 * Registers the Notka and Ramka promocyjna styles of `core/group`.
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
