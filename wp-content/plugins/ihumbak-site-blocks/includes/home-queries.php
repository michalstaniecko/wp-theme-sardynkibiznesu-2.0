<?php
/**
 * Query variations of the Sekcje strony głównej Hero and Najnowsze (spec #36, #58).
 *
 * - `"namespace":"ihumbak/hero"`: exactly one post: the newest sticky post, else the newest
 *   post. Core's own sticky handling ("include") would prepend the sticky post to the newest
 *   one and show two.
 * - `"namespace":"ihumbak/latest"`: the newest posts after the hero. The block keeps
 *   `offset: 1` (correct while nothing is sticky, and what the editor preview shows); on the
 *   server the hero post is excluded instead, so a sticky hero does not hide the newest post.
 *
 * Recognised server-side by the `namespace` attribute, like „Z tej kategorii”
 * (same-category-query.php). The editor side (names, isActive) is in assets/editor.js.
 */

namespace Ihumbak\SiteBlocks\HomeQueries;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HERO   = 'ihumbak/hero';
const LATEST = 'ihumbak/latest';

/** Post shown by the hero: newest published sticky post, else the newest post; 0 if none. */
function hero_post_id(): int {
	$sticky = array_filter( array_map( 'intval', (array) get_option( 'sticky_posts', array() ) ) );
	$args   = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
	);
	if ( $sticky ) {
		$ids = get_posts( $args + array( 'post__in' => $sticky ) );
		if ( $ids ) {
			return (int) $ids[0];
		}
	}
	$ids = get_posts( $args );
	return $ids ? (int) $ids[0] : 0;
}

function hero_query_vars( array $query ): array {
	$query['post__in']            = array( hero_post_id() ?: 0 );
	$query['posts_per_page']      = 1;
	$query['offset']              = 0;
	$query['ignore_sticky_posts'] = true;
	return $query;
}

function latest_query_vars( array $query ): array {
	$query['post__not_in']        = array_merge( $query['post__not_in'] ?? array(), array( hero_post_id() ) );
	$query['offset']              = 0;
	$query['ignore_sticky_posts'] = true;
	return $query;
}

/** @return callable-string|null Query vars filter of the variation, if the block is one. */
function filter_for( array $parsed_block ): ?string {
	if ( 'core/query' !== ( $parsed_block['blockName'] ?? '' ) ) {
		return null;
	}
	return match ( $parsed_block['attrs']['namespace'] ?? '' ) {
		HERO    => __NAMESPACE__ . '\hero_query_vars',
		LATEST  => __NAMESPACE__ . '\latest_query_vars',
		default => null,
	};
}

add_filter(
	'pre_render_block',
	static function ( $pre_render, array $parsed_block ) {
		$filter = filter_for( $parsed_block );
		if ( $filter ) {
			add_filter( 'query_loop_block_query_vars', $filter );
		}
		return $pre_render;
	},
	10,
	2
);

add_filter(
	'render_block_core/query',
	static function ( string $html, array $parsed_block ): string {
		$filter = filter_for( $parsed_block );
		if ( $filter ) {
			remove_filter( 'query_loop_block_query_vars', $filter );
		}
		return $html;
	},
	10,
	2
);
