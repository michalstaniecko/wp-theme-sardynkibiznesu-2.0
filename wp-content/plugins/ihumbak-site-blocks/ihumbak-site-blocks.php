<?php
/**
 * Plugin Name:       iHumbak Site Blocks
 * Description:       Page frame blocks of theme 3.0: sticky site header, search toggle and search bar (Interactivity API), the accordion filter of the navigation overlay, share buttons, author social links, the Query variation „Z tej kategorii” and the daily GA4 sync of post popularity (`_sb_popularity`).
 * Version:           1.2.0
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            iHumbak
 * Text Domain:       ihumbak-site-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_SITE_BLOCKS_FILE = __FILE__;

require_once __DIR__ . '/includes/blocks.php';
require_once __DIR__ . '/includes/navigation-overlay.php';
require_once __DIR__ . '/includes/author-social-links.php';
require_once __DIR__ . '/includes/same-category-query.php';
require_once __DIR__ . '/includes/popularity.php';
