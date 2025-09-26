<?php

/**
 * Plugin Name: Ihumbak Anchor 2
 * Description: A plugin to add anchor links to headings in posts and pages.
 * Version: 1.0
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
		exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-ihumbak-anchor.php' );

// Initialize the plugin
function ihumbak_anchor_init() {
	$ihumbak_anchor = new Ihumbak_Anchor();
}
add_action( 'plugins_loaded', 'ihumbak_anchor_init' );
