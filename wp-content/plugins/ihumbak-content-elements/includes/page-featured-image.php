<?php
/**
 * Per-page choice to display the Obrazek wyróżniający on the page itself (#81): off by default,
 * also for a page with no saved value. Cards and the social share image don't read it. The plugin
 * owns the meta and the editor panel; the theme decides whether to render its own block (ADR-0001).
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\PageFeaturedImage;

defined( 'ABSPATH' ) || exit;

/** Same string as PAGE_SHOW_FEATURED_IMAGE_META in the theme's functions.php (sardynkibiznesu-3.0). */
const META_SHOW = '_sb_page_show_featured_image';

add_action( 'init', __NAMESPACE__ . '\\register_meta' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_panel' );

/**
 * Registers the meta key on pages.
 */
function register_meta(): void {
	register_post_meta(
		'page',
		META_SHOW,
		array(
			'type'              => 'boolean',
			'single'            => true,
			'default'           => false,
			'label'             => 'Pokazuj obrazek wyróżniający na stronie',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'show_in_rest'      => true,
			'auth_callback'     => static fn ( $allowed, $meta_key, $post_id ): bool => current_user_can( 'edit_post', $post_id ),
		)
	);
}

/**
 * The „Obrazek na stronie” panel in the page editor sidebar (pages only; not in the Site Editor).
 */
function enqueue_panel(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_script(
		'ihumbak-content-elements-page-featured-image-panel',
		plugins_url( 'assets/page-featured-image-panel.js', IHUMBAK_CONTENT_ELEMENTS_FILE ),
		array( 'wp-components', 'wp-core-data', 'wp-data', 'wp-editor', 'wp-element', 'wp-plugins' ),
		IHUMBAK_CONTENT_ELEMENTS_VERSION,
		true
	);
}
