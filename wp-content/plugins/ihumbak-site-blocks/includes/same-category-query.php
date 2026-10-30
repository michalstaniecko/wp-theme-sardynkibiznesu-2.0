<?php
/**
 * Query variation „Z tej kategorii” (spec #36, decision #27 pkt 5): a
 * `core/query` with `"namespace":"ihumbak/same-category"` lists posts from the
 * categories of the current post, without the current post. The editor side
 * (inserter entry, isActive) is in assets/editor.js.
 *
 * Server side, the variation is recognised by its `namespace` attribute
 * (`pre_render_block`), and only while that block renders its post template
 * gets the extra query vars (`query_loop_block_query_vars`). With no other post
 * in the category the whole block, heading included, renders nothing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_SAME_CATEGORY_QUERY = 'ihumbak/same-category';

function ihumbak_site_blocks_is_same_category_query( array $parsed_block ): bool {
	return 'core/query' === ( $parsed_block['blockName'] ?? '' )
		&& IHUMBAK_SAME_CATEGORY_QUERY === ( $parsed_block['attrs']['namespace'] ?? '' );
}

/**
 * Query vars of the variation: categories of the current post, current post excluded.
 *
 * @param array<string, mixed> $query Vars built by core from the block's `query` attribute.
 * @return array<string, mixed>
 */
function ihumbak_site_blocks_same_category_query_vars( array $query ): array {
	$post_id    = (int) get_the_ID();
	$categories = $post_id ? wp_get_post_categories( $post_id ) : array();

	if ( ! $categories ) {
		$query['post__in'] = array( 0 ); // No category, nothing related.
		return $query;
	}

	$query['category__in']        = $categories;
	$query['post__not_in']        = array_merge( $query['post__not_in'] ?? array(), array( $post_id ) );
	$query['ignore_sticky_posts'] = true;
	return $query;
}

add_filter(
	'pre_render_block',
	static function ( $pre_render, array $parsed_block ) {
		if ( ihumbak_site_blocks_is_same_category_query( $parsed_block ) ) {
			add_filter( 'query_loop_block_query_vars', 'ihumbak_site_blocks_same_category_query_vars' );
		}
		return $pre_render;
	},
	10,
	2
);

add_filter(
	'render_block_core/query',
	static function ( string $html, array $parsed_block ): string {
		if ( ! ihumbak_site_blocks_is_same_category_query( $parsed_block ) ) {
			return $html;
		}
		remove_filter( 'query_loop_block_query_vars', 'ihumbak_site_blocks_same_category_query_vars' );
		// core/post-template renders '' without posts: drop the heading too.
		return str_contains( $html, 'wp-block-post-template' ) ? $html : '';
	},
	10,
	2
);
