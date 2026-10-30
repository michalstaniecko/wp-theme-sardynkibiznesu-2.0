<?php
/**
 * Plugin Name:       iHumbak Tracking
 * Description:       Google Tag Manager container and verification tags in <head>. No other trackers, no consent logic: consent lives in GTM (CookieYes, Consent Mode v2).
 * Version:           1.0.0
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            iHumbak
 * Text Domain:       ihumbak-tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_TRACKING_OPTION_GTM_ID    = 'ihumbak_tracking_gtm_id';
const IHUMBAK_TRACKING_OPTION_HEAD_TAGS = 'ihumbak_tracking_head_tags';

add_action( 'wp_head', 'ihumbak_tracking_head', 1 );
add_action( 'wp_body_open', 'ihumbak_tracking_body_open', 1 );

/**
 * Whether anything may be printed on this request: only on production
 * (set WP_ENVIRONMENT_TYPE=local elsewhere) and only for visitors who are not logged in.
 */
function ihumbak_tracking_is_enabled(): bool {
	return 'production' === wp_get_environment_type() && ! is_user_logged_in();
}

function ihumbak_tracking_head(): void {
	if ( ! ihumbak_tracking_is_enabled() ) {
		return;
	}

	$gtm_id = (string) get_option( IHUMBAK_TRACKING_OPTION_GTM_ID, '' );

	if ( '' !== $gtm_id ) {
		printf(
			"<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\nnew Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\nj=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n})(window,document,'script','dataLayer','%s');</script>\n<!-- End Google Tag Manager -->\n",
			esc_js( $gtm_id )
		);
	}

	$head_tags = (string) get_option( IHUMBAK_TRACKING_OPTION_HEAD_TAGS, '' );

	if ( '' !== trim( $head_tags ) ) {
		// Printed verbatim on purpose: the owner pastes domain verification tags here.
		echo $head_tags . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

function ihumbak_tracking_body_open(): void {
	$gtm_id = (string) get_option( IHUMBAK_TRACKING_OPTION_GTM_ID, '' );

	if ( '' === $gtm_id || ! ihumbak_tracking_is_enabled() ) {
		return;
	}

	printf(
		'<!-- Google Tag Manager (noscript) -->' . "\n" .
		'<noscript><iframe src="%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n" .
		'<!-- End Google Tag Manager (noscript) -->' . "\n",
		esc_url( 'https://www.googletagmanager.com/ns.html?id=' . rawurlencode( $gtm_id ) )
	);
}
