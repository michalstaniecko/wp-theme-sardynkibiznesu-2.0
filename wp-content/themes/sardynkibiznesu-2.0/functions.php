<?php

include_once get_stylesheet_directory() . '/inc/theme-support.php';
include_once get_stylesheet_directory() . '/inc/theme-enqueue.php';
include_once get_stylesheet_directory() . '/inc/theme-head.php';
include_once get_stylesheet_directory() . '/inc/theme-footer.php';
include_once get_stylesheet_directory() . '/inc/tracking-codes.php';
include_once get_stylesheet_directory() . '/inc/menus.php';
include_once get_stylesheet_directory() . '/inc/register-widgets.php';
include_once get_stylesheet_directory() . '/inc/converter-pro.php';
include_once get_stylesheet_directory() . '/inc/acf.php';
include_once get_stylesheet_directory() . '/inc/acf-blocks.php';
include_once get_stylesheet_directory() . '/inc/shortcodes.php';
include_once get_stylesheet_directory() . '/inc/article-hooks.php';
include_once get_stylesheet_directory() . '/inc/seo-schema.php';
include_once get_stylesheet_directory() . '/cmsmasters-shortcodes/shortcodes.php';

//add_action('all', 'log_hook_calls');

function log_hook_calls($tag) {
  global $wp_actions, $wp_filters;
  // Restrict logging to requests from your IP - your IP address goes here
  $client_ip = "89.64.99.95";
  // The full path to your log file:
  $log_file_path = ABSPATH . '/hook_calls.log';
  if ($_SERVER['REMOTE_ADDR'] !== $client_ip) {
    return false;
  }
  if (!WP_DEBUG_LOG) {
    return false;
  }

  error_log(date("d-m-Y, H:i:s") . ": " . current_filter() . ' : ' . $tag . "\n", 3, $log_file_path);
}
