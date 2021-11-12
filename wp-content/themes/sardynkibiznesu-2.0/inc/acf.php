<?php
// Define path and URL to the ACF plugin.
define('MY_ACF_PATH', get_stylesheet_directory() . '/inc/advanced-custom-fields-pro/');
define('MY_ACF_URL', get_stylesheet_directory_uri() . '/inc/advanced-custom-fields-pro/');

if (!defined('ACFFA_PUBLIC_PATH')) {
  define('ACFFA_PUBLIC_PATH', get_stylesheet_directory_uri() . '/inc/advanced-custom-fields-font-awesome/');
}

// Method 1: Filter.
function my_acf_google_map_api($api) {
  $api['key'] = GMAP_KEY;
  return $api;
}

//add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

// Include the ACF plugin.
include_once( MY_ACF_PATH . 'acf.php' );

// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url($url) {
  return MY_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
function my_acf_settings_show_admin($show_admin) {
  return true;
}

add_filter('acf/settings/save_json', 'my_acf_json_save_point');

function my_acf_json_save_point($path) {

  // update path
  $path = get_stylesheet_directory() . '/acf-fields';

  if (!file_exists($path)) mkdir($path, 0775);

  // return
  return $path;

}

add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point($paths) {

  // remove original path (optional)
  unset($paths[0]);


  // append path
  $paths[] = get_stylesheet_directory() . '/acf-fields';


  // return
  return $paths;

}



