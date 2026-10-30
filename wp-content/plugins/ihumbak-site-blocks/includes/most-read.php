<?php
/**
 * „Najczęściej czytane” (spec #36, ticket #55): which posts the block
 * `ihumbak/most-read` lists. The block markup lives in blocks/most-read/render.php.
 *
 * - Order: the popularity contract of popularity.php (`order_args()`), so before
 *   the first GA4 sync the block shows the newest posts.
 * - Scope `auto`: on a category archive only that category (with its subcategories);
 *   elsewhere all posts. Scope `all`: always all posts.
 * - On a single post the post itself is left out.
 */

namespace Ihumbak\SiteBlocks\MostRead;

use Ihumbak\SiteBlocks\Popularity;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DEFAULT_COUNT = 5;
const MAX_COUNT     = 10;

/**
 * Category the block is narrowed to on the current request, 0 = none.
 */
function scoped_category( string $scope ): int {
	return 'all' !== $scope && is_category() ? (int) get_queried_object_id() : 0;
}

/**
 * @return \WP_Post[]
 */
function posts( int $count, int $category ): array {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( MAX_COUNT, $count ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) + Popularity\order_args();

	if ( $category ) {
		$args['cat'] = $category;
	}
	if ( is_singular( 'post' ) ) {
		$args['post__not_in'] = array( (int) get_queried_object_id() ); // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- one id, small list.
	}

	return ( new \WP_Query( $args ) )->posts;
}
