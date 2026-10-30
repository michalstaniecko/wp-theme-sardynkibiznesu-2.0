<?php
/**
 * Plugin Name:       iHumbak Content Elements
 * Description:       Elementy treści (Przycisk i kolejne) oraz obsługa ich starych tagów z motywu 2.0, niezależnie od motywu (ADR-0001). Wygląd pochodzi z motywu; wtyczka dodaje tylko minimalny CSS na tokenach motywu z fallbackami.
 * Version:           1.0.0
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            iHumbak
 * Text Domain:       ihumbak-content-elements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_CONTENT_ELEMENTS_VERSION = '1.0.0';
const IHUMBAK_CONTENT_ELEMENTS_FILE    = __FILE__;

require_once __DIR__ . '/includes/button.php';
require_once __DIR__ . '/includes/group-styles.php';
require_once __DIR__ . '/includes/badges.php';
require_once __DIR__ . '/includes/product-card.php';
require_once __DIR__ . '/includes/podcast.php';
