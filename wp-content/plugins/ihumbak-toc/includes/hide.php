<?php
/**
 * "Ukryj spis treści": a per-post switch that turns off every Spis treści of that post — the
 * block (inline and rail), [ihumbak_anchor_toc] and the v1 [ihumbak_anchor_menu]. Kotwice stay:
 * headings keep their ids, so links to them still work.
 *
 * Stored as the post meta HIDE_META_KEY; the editor checkbox lives in assets/hide-panel.js.
 */

namespace Ihumbak\Toc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HIDE_META_KEY     = '_ihumbak_toc_hidden';
const HIDE_POST_TYPES   = array( 'post', 'page' );
const HIDE_PANEL_SCRIPT = 'ihumbak-toc-hide-panel';

add_action(
	'init',
	static function (): void {
		foreach ( HIDE_POST_TYPES as $post_type ) {
			register_post_meta(
				$post_type,
				HIDE_META_KEY,
				array(
					'type'          => 'boolean',
					'single'        => true,
					'default'       => false,
					'show_in_rest'  => true,
					'auth_callback' => static fn ( $allowed, $meta_key, $post_id ): bool => current_user_can( 'edit_post', (int) $post_id ),
				)
			);
		}
	}
);

add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		wp_enqueue_script(
			HIDE_PANEL_SCRIPT,
			plugins_url( 'assets/hide-panel.js', __DIR__ ),
			array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data' ),
			(string) filemtime( dirname( __DIR__ ) . '/assets/hide-panel.js' ),
			array( 'in_footer' => true )
		);
	}
);

/**
 * Whether the editor turned off the Spis treści of this post.
 */
function is_toc_hidden( int $post_id ): bool {
	return $post_id > 0 && (bool) get_post_meta( $post_id, HIDE_META_KEY, true );
}
