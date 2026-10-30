<?php
/**
 * Plugin Name:       iHumbak TOC
 * Description:       Spis treści i Kotwice. Renders the legacy [ihumbak_anchor] / [ihumbak_anchor_menu] tags server-side, without jQuery and without the _ihumbak_anchor_links meta.
 * Version:           1.0.0
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            iHumbak
 * Text Domain:       ihumbak-toc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/toc-markup.php';
require_once __DIR__ . '/includes/anchors-v1.php';
require_once __DIR__ . '/includes/anchors.php';
require_once __DIR__ . '/includes/block.php';
