<?php
/**
 * One-time copy of the 2.0 newsletter settings (ACF options of the 2.0 theme) on activation:
 * options_newsletter_api_key → API key, options_mailerlite_default_token → default group,
 * options_getresponse_default_thankyou (ACF link: array with `url`, or a URL) → Strona podziękowania.
 *
 * Runs once (flag option), copies only into empty settings, never deletes the ACF options.
 */

namespace Ihumbak\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OPTION_ACF_IMPORTED = 'ihumbak_newsletter_acf_imported';

function activate(): void {
	if ( get_option( OPTION_ACF_IMPORTED ) ) {
		return;
	}

	$key = trim( (string) get_option( 'options_newsletter_api_key', '' ) );
	if ( '' !== $key && '' === (string) get_option( OPTION_API_KEY, '' ) ) {
		update_option( OPTION_API_KEY, $key );
	}

	$group = sanitize_group( get_option( 'options_mailerlite_default_token', '' ) );
	if ( '' !== $group && '' === default_group() ) {
		update_option( OPTION_GROUP, $group );
	}

	$thanks = get_option( 'options_getresponse_default_thankyou', '' );
	$url    = is_array( $thanks ) ? (string) ( $thanks['url'] ?? '' ) : (string) $thanks;
	$page   = '' !== $url ? page_id_from_url( $url ) : 0;
	if ( $page && ! (int) get_option( OPTION_THANKYOU, 0 ) ) {
		update_option( OPTION_THANKYOU, $page );
	}

	update_option( OPTION_ACF_IMPORTED, gmdate( 'c' ), false );
}

/**
 * Page ID for a URL of this site. Matches by path, so a production URL also
 * resolves on staging/local copies (url_to_postid() rejects foreign hosts).
 */
function page_id_from_url( string $url ): int {
	$id = url_to_postid( $url );
	if ( $id ) {
		return $id;
	}
	$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
	$page = '' !== $path ? get_page_by_path( $path ) : null;
	return $page ? (int) $page->ID : 0;
}
