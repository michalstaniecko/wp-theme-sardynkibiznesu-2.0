<?php
/**
 * Query variation „Z tej kategorii” (spec #36, decision #27 pkt 5): a
 * `core/query` with `"namespace":"ihumbak/same-category"` lists posts from the
 * categories of the current post, without the current post. The editor side
 * (inserter entry, isActive) is in assets/editor.js.
 *
 * Server side (query-variations.php), the variation is recognised by its
 * `namespace` attribute, and only while that block renders its post template
 * gets the extra query vars. With no other post in the category the whole
 * block, heading included, renders nothing.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_SAME_CATEGORY_QUERY = 'ihumbak/same-category';

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

\Ihumbak\SiteBlocks\QueryVariations\register(
	IHUMBAK_SAME_CATEGORY_QUERY,
	'ihumbak_site_blocks_same_category_query_vars',
	// core/post-template renders '' without posts: drop the heading too.
	static fn ( string $html ): string => str_contains( $html, 'wp-block-post-template' ) ? $html : ''
);
