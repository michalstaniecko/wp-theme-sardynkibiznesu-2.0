<?php
/**
 * Post popularity (spec #36, research #28, ticket #53): once a day every
 * published post gets `_sb_popularity` = page views of the last 30 days from GA4.
 * „Najczęściej czytane” and the category sort (#55) read only this meta.
 *
 * - Meta `_sb_popularity` (integer, default 0) is written to EVERY published post,
 *   0 included, because a query with `meta_key` drops posts without the key. New
 *   posts get 0 when published.
 * - Sync is atomic: fetch and map everything first, write meta only after a
 *   successful fetch. On failure the old values stay and `last_error` is recorded;
 *   7 days without success → notice in the dashboard.
 * - Sync metadata: option `sb_popularity_sync` (not autoloaded):
 *   updated_at (last success), window_days, source, rows, matched, unmatched_top
 *   (top 20 paths without a post), last_error, last_attempt_at, failing_since,
 *   subjectToThresholding, dataLossFromOtherRow, sampled.
 * - After a successful sync the LiteSpeed tag `sb_popularity` is purged (blocks
 *   that read the meta add this tag when they render).
 * - Schedule: WP-Cron event daily at 05:00 site time. In production the host's
 *   system cron calls wp-cron.php (or `wp cron event run --due-now`) with
 *   `DISABLE_WP_CRON` set, because LiteSpeed-cached pages rarely trigger WP-Cron.
 *   Manual run: `wp sb popularity sync`.
 * - No JS beacon: the data comes from GA4 only.
 */

namespace Ihumbak\SiteBlocks\Popularity;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/popularity-ga4.php';

const META          = '_sb_popularity';
const OPTION        = 'sb_popularity_sync';
const CRON_HOOK     = 'ihumbak_site_blocks_popularity_sync';
const CACHE_TAG     = 'sb_popularity';
const STALE_AFTER   = 7 * DAY_IN_SECONDS;
const UNMATCHED_TOP = 20;

add_action( 'init', __NAMESPACE__ . '\\register_meta' );
add_action( 'init', __NAMESPACE__ . '\\schedule' );
add_action( CRON_HOOK, __NAMESPACE__ . '\\run_scheduled' );
add_action( 'transition_post_status', __NAMESPACE__ . '\\on_publish', 10, 3 );
add_action( 'admin_notices', __NAMESPACE__ . '\\admin_notice' );
register_deactivation_hook( IHUMBAK_SITE_BLOCKS_FILE, __NAMESPACE__ . '\\unschedule' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	\WP_CLI::add_command( 'sb popularity sync', __NAMESPACE__ . '\\cli_sync' );
}

function register_meta(): void {
	register_post_meta(
		'post',
		META,
		array(
			'type'          => 'integer',
			'single'        => true,
			'default'       => 0,
			'show_in_rest'  => false,
			'auth_callback' => '__return_false',
		)
	);
}

/**
 * Has a sync ever succeeded? Before that the meta is missing or all 0.
 */
function is_synced(): bool {
	$state = get_option( OPTION );
	return is_array( $state ) && ! empty( $state['updated_at'] );
}

/**
 * WP_Query ordering „by popularity”: `meta_value_num DESC`, then `date DESC`.
 * Before the first successful sync it is plain date order, because posts that
 * existed before the plugin have no meta row yet and a `meta_key` query would drop them.
 *
 * @return array<string, mixed>
 */
function order_args(): array {
	if ( ! is_synced() ) {
		return array(
			'orderby' => 'date',
			'order'   => 'DESC',
		);
	}
	return array(
		'meta_key' => META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- the popularity contract (spec #36).
		'orderby'  => array(
			'meta_value_num' => 'DESC',
			'date'           => 'DESC',
		),
	);
}

/**
 * Newly published posts get 0 so they show up in popularity queries right away.
 */
function on_publish( string $new_status, string $old_status, \WP_Post $post ): void {
	if ( 'publish' === $new_status && 'publish' !== $old_status && 'post' === $post->post_type ) {
		add_post_meta( $post->ID, META, 0, true );
	}
}

/**
 * Daily event at 05:00 site time (GA4 daily data is processed ~12 h after the day ends).
 */
function schedule(): void {
	if ( wp_next_scheduled( CRON_HOOK ) ) {
		return;
	}
	$next = new \DateTimeImmutable( 'today 05:00', wp_timezone() );
	if ( $next->getTimestamp() <= time() ) {
		$next = $next->modify( '+1 day' );
	}
	wp_schedule_event( $next->getTimestamp(), 'daily', CRON_HOOK );
}

function unschedule(): void {
	wp_clear_scheduled_hook( CRON_HOOK );
}

function run_scheduled(): void {
	sync();
}

/**
 * Fetches page views and writes `_sb_popularity` to every published post.
 *
 * @return array<string, mixed>|WP_Error Sync metadata on success.
 */
function sync(): array|WP_Error {
	$window = max( 1, (int) apply_filters( 'sb_popularity_window_days', 30 ) );
	$source = new Ga4_Source();
	$state  = get_option( OPTION );
	$state  = is_array( $state ) ? $state : array();
	$now    = time();

	$state['last_attempt_at'] = $now;

	$result = $source->fetch( $window );
	if ( is_wp_error( $result ) ) {
		$state['last_error']    = $result->get_error_message();
		$state['failing_since'] = empty( $state['failing_since'] ) ? $now : $state['failing_since'];
		update_option( OPTION, $state, false );
		return $result;
	}

	$totals    = array();
	$unmatched = array();
	$lookup    = array();
	foreach ( $result['rows'] as $row ) {
		$path = $row['path'];
		if ( ! array_key_exists( $path, $lookup ) ) {
			$lookup[ $path ] = post_for_path( $path );
		}
		$post_id = $lookup[ $path ];
		if ( $post_id > 0 ) {
			$totals[ $post_id ] = ( $totals[ $post_id ] ?? 0 ) + $row['views'];
		} elseif ( 0 === $post_id ) {
			$unmatched[ $path ] = ( $unmatched[ $path ] ?? 0 ) + $row['views'];
		}
	}

	$published = get_posts(
		array(
			'post_type'        => 'post',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);
	foreach ( $published as $post_id ) {
		update_post_meta( $post_id, META, (int) ( $totals[ $post_id ] ?? 0 ) );
	}

	arsort( $unmatched );
	$top = array();
	foreach ( array_slice( $unmatched, 0, UNMATCHED_TOP, true ) as $path => $views ) {
		$top[] = array( 'path' => (string) $path, 'views' => $views );
	}

	$state = array(
		'updated_at'      => $now,
		'window_days'     => $window,
		'source'          => 'ga4',
		'rows'            => count( $result['rows'] ),
		'matched'         => count( $totals ),
		'unmatched_top'   => $top,
		'last_error'      => '',
		'last_attempt_at' => $now,
		'failing_since'   => 0,
	) + $result['flags'];
	update_option( OPTION, $state, false );

	do_action( 'litespeed_purge', CACHE_TAG );

	return $state;
}

/**
 * Published post a GA4 `pagePath` points to.
 *
 * @return int Post ID; 0 = no post (logged as unmatched); -1 = some other
 *             content (page, draft, attachment), ignored.
 */
function post_for_path( string $path ): int {
	$path = '/' . ltrim( $path, '/' );
	$id   = 0;

	// GA4 may report non-ASCII slugs decoded or percent-encoded: try both.
	foreach ( array_unique( array( $path, rawurldecode( $path ), encode_path( rawurldecode( $path ) ) ) ) as $candidate ) {
		$id = url_to_postid( home_url( $candidate ) );
		if ( $id ) {
			break;
		}
	}

	if ( ! $id ) {
		$id = post_for_old_slug( $path );
	}
	if ( ! $id ) {
		return 0;
	}

	return 'post' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ? (int) $id : -1;
}

/**
 * Fallback for renamed posts: the last slug-like segment (a trailing page number
 * is skipped) against `_wp_old_slug`.
 */
function post_for_old_slug( string $path ): int {
	$segments = array_values( array_filter( explode( '/', $path ), 'strlen' ) );
	if ( $segments && ctype_digit( (string) end( $segments ) ) ) {
		array_pop( $segments );
	}
	if ( ! $segments ) {
		return 0;
	}
	$slug = (string) end( $segments );

	$ids = get_posts(
		array(
			'post_type'        => 'post',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- once a day, unmatched paths only.
				array(
					'key'     => '_wp_old_slug',
					'value'   => array_unique( array( $slug, rawurldecode( $slug ), strtolower( rawurlencode( rawurldecode( $slug ) ) ) ) ),
					'compare' => 'IN',
				),
			),
		)
	);
	return $ids ? (int) $ids[0] : 0;
}

function encode_path( string $path ): string {
	return implode( '/', array_map( fn( $s ) => strtolower( rawurlencode( $s ) ), explode( '/', $path ) ) );
}

/**
 * Dashboard notice after 7 days without a successful sync.
 */
function admin_notice(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$state = get_option( OPTION );
	if ( ! is_array( $state ) || ! is_stale( $state, time() ) ) {
		return;
	}

	$last = empty( $state['updated_at'] )
		? 'Synchronizacja nie udała się ani razu.'
		: sprintf( 'Ostatnia udana synchronizacja: %s.', wp_date( 'j.m.Y H:i', (int) $state['updated_at'] ) );

	printf(
		'<div class="notice notice-error sb-popularity-notice"><p><strong>%1$s</strong> %2$s</p><p>%3$s <code>%4$s</code></p><p>%5$s</p></div>',
		esc_html__( 'Popularność wpisów (GA4) nie jest aktualizowana od ponad 7 dni.', 'ihumbak-site-blocks' ),
		esc_html( $last ),
		esc_html__( 'Ostatni błąd:', 'ihumbak-site-blocks' ),
		esc_html( (string) ( $state['last_error'] ?? '' ) ),
		esc_html__( '„Najczęściej czytane” pokazują stare dane. Sprawdź stałe SB_GA4_PROPERTY_ID i SB_GA4_CREDENTIALS_FILE w wp-config.php oraz systemowy cron; ręcznie: wp sb popularity sync.', 'ihumbak-site-blocks' )
	);
}

/**
 * @param array<string, mixed> $state Sync metadata.
 */
function is_stale( array $state, int $now ): bool {
	$since = ! empty( $state['updated_at'] ) ? (int) $state['updated_at'] : (int) ( $state['failing_since'] ?? 0 );
	return $since > 0 && $now - $since > STALE_AFTER;
}

/**
 * Runs the popularity sync now.
 *
 * ## EXAMPLES
 *
 *     wp sb popularity sync
 *
 * @param array<int, string> $args Unused.
 */
function cli_sync( array $args ): void {
	$result = sync();
	if ( is_wp_error( $result ) ) {
		\WP_CLI::error( $result->get_error_message() );
	}
	foreach ( $result['unmatched_top'] as $row ) {
		\WP_CLI::log( sprintf( 'Bez wpisu: %s (%d)', $row['path'], $row['views'] ) );
	}
	\WP_CLI::success( sprintf( 'Wierszy GA4: %d, dopasowanych wpisów: %d.', $result['rows'], $result['matched'] ) );
}
