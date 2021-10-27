<?php
/**
 *
 * Plugin Name: iHumbak Shortcodes
 */

add_shortcode( 'sardynka', 'sardynka' );
function sardynka() {
  return '<img src="'.get_stylesheet_directory_uri().'/img/icon-sardynka.jpg" class="icon--inline" />';
}