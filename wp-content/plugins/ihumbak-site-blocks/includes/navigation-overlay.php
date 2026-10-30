<?php
/**
 * Accordion in the navigation overlay (decision #29, spec #36), without a script.
 *
 * A core/navigation inside the navigation-overlay template part inherits
 * `overlayOpenedBy` from the header nav's context, so core's
 * `state.isSubmenuOpen` is always true there and every submenu is expanded.
 * Giving each submenu its own, closed `overlayOpenedBy` shadows the inherited
 * value: core's native toggleMenuOnClick then drives a real aria-expanded, and
 * opening one section closes the others (core's focusout handling). Visibility
 * of the submenu is CSS on `[aria-expanded="true"] ~ .wp-block-navigation__submenu-container`.
 *
 * RISK: depends on core's internal Interactivity context of core/navigation.
 * Re-check after every WordPress update (docs/wp-update-checklist.md).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'render_block_core/navigation', 'ihumbak_site_blocks_overlay_accordion', 10, 2 );

/**
 * @param string               $html  Rendered core/navigation.
 * @param array<string, mixed> $block Parsed block.
 */
function ihumbak_site_blocks_overlay_accordion( string $html, array $block ): string {
	$attrs = $block['attrs'] ?? array();

	if ( empty( $attrs['_isWithinOverlayTemplatePart'] ) || 'click' !== ( $attrs['submenuVisibility'] ?? '' ) ) {
		return $html;
	}

	$tags = new WP_HTML_Tag_Processor( $html );

	while ( $tags->next_tag(
		array(
			'tag_name'   => 'LI',
			'class_name' => 'wp-block-navigation-submenu',
		)
	) ) {
		$context = json_decode( (string) $tags->get_attribute( 'data-wp-context' ), true );
		$context = is_array( $context ) ? $context : array();

		$context['overlayOpenedBy'] = array(
			'click' => false,
			'hover' => false,
			'focus' => false,
		);

		$tags->set_attribute( 'data-wp-context', wp_json_encode( $context ) );
	}

	return $tags->get_updated_html();
}
