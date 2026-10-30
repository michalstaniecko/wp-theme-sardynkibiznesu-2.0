<?php
/**
 * Sorting of category archives (spec #36, ticket #55): `?sort=popular` (most read,
 * popularity contract of popularity.php) and `?sort=oldest`; no parameter = newest.
 *
 * - Works on the main query, so the template's inherited `core/query`, its pagination
 *   (`get_pagenum_link()` keeps the query string) and the post count follow the URL.
 * - Unknown values are ignored (newest).
 * - Sorted views are `noindex, follow`: same posts as the clean category URL, which
 *   stays the canonical one.
 * - The links are rendered by the block `ihumbak/listing-sort` (blocks/listing-sort).
 */

namespace Ihumbak\SiteBlocks\ListingSort;

use Ihumbak\SiteBlocks\Popularity;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const QUERY_VAR = 'sort';
const NEWEST    = 'newest';

add_filter( 'query_vars', __NAMESPACE__ . '\\query_vars' );
add_action( 'pre_get_posts', __NAMESPACE__ . '\\pre_get_posts' );
add_filter( 'wp_robots', __NAMESPACE__ . '\\wp_robots' );
add_filter( 'wpseo_robots_array', __NAMESPACE__ . '\\wpseo_robots_array' );

/**
 * Sort options in display order: value => label. NEWEST is the default (no parameter).
 *
 * @return array<string, string>
 */
function options(): array {
	return array(
		NEWEST    => __( 'Najnowsze', 'ihumbak-site-blocks' ),
		'popular' => __( 'Najczęściej czytane', 'ihumbak-site-blocks' ),
		'oldest'  => __( 'Najstarsze', 'ihumbak-site-blocks' ),
	);
}

/**
 * @param string[] $vars Public query vars.
 * @return string[]
 */
function query_vars( array $vars ): array {
	$vars[] = QUERY_VAR;
	return $vars;
}

/**
 * Sort of the given query (default: main query): one of the keys of options().
 */
function current( ?\WP_Query $query = null ): string {
	$query = $query ?? $GLOBALS['wp_query'];
	$value = $query instanceof \WP_Query ? (string) $query->get( QUERY_VAR ) : '';
	return isset( options()[ $value ] ) ? $value : NEWEST;
}

function applies( \WP_Query $query ): bool {
	return ! is_admin() && $query->is_main_query() && $query->is_category();
}

function pre_get_posts( \WP_Query $query ): void {
	if ( ! applies( $query ) ) {
		return;
	}
	switch ( current( $query ) ) {
		case 'popular':
			foreach ( Popularity\order_args() as $key => $value ) {
				$query->set( $key, $value );
			}
			// The popularity tag makes LiteSpeed purge this view after each sync.
			do_action( 'litespeed_tag_add', Popularity\CACHE_TAG );
			break;
		case 'oldest':
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'ASC' );
			break;
	}
}

function is_sorted_view(): bool {
	global $wp_query;
	return $wp_query instanceof \WP_Query && applies( $wp_query ) && NEWEST !== current( $wp_query );
}

/**
 * @param array<string, bool|string> $robots
 * @return array<string, bool|string>
 */
function wp_robots( array $robots ): array {
	if ( is_sorted_view() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['index'] );
	}
	return $robots;
}

/**
 * Yoast SEO prints its own robots meta (it replaces core's wp_robots output).
 *
 * @param array<string, string> $robots
 * @return array<string, string>
 */
function wpseo_robots_array( array $robots ): array {
	if ( is_sorted_view() ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
	}
	return $robots;
}

/**
 * URL of the current category archive (page 1) with the given sort.
 */
function url( string $sort ): string {
	$term = get_queried_object();
	$base = $term instanceof \WP_Term ? get_term_link( $term ) : '';
	if ( ! is_string( $base ) || '' === $base ) {
		return '';
	}
	return NEWEST === $sort ? $base : add_query_arg( QUERY_VAR, $sort, $base );
}
