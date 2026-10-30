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

const IHUMBAK_TRACKING_OPTION_GTM_ID = 'ihumbak_tracking_gtm_id';

add_action( 'wp_head', 'ihumbak_tracking_head', 1 );

function ihumbak_tracking_head(): void {
	$gtm_id = (string) get_option( IHUMBAK_TRACKING_OPTION_GTM_ID, '' );

	if ( '' !== $gtm_id ) {
		printf(
			"<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\nnew Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\nj=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n})(window,document,'script','dataLayer','%s');</script>\n<!-- End Google Tag Manager -->\n",
			esc_js( $gtm_id )
		);
	}
}
