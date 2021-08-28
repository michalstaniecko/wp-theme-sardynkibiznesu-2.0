<?php
/**
 *
 * Plugin Name: iHumbak Newsletter Form
 *
 */


include plugin_dir_path(__FILE__).'getResponse/addContact.php';
include plugin_dir_path(__FILE__).'inc/newsletter-form.php';
include plugin_dir_path(__FILE__).'inc/getresponse-form.php';

add_action('wp_enqueue_scripts', 'plugin_theme_scripts');

function plugin_theme_scripts() {
  wp_enqueue_style( 'bootstrap-cdn', plugin_dir_url(__FILE__).'/css/bootstrap.min.css' );
  wp_enqueue_style( 'newsletter-form', plugin_dir_url(__FILE__).'/css/newsletter-form.css', false, false, 'all' );
  wp_enqueue_script('bootstrap.min', plugin_dir_url(__FILE__).'/js/bootstrap.min.js', array('jquery'), false, true);
  wp_enqueue_script( 'get-response-add-contact', plugin_dir_url(__FILE__).'getResponse/getresponse.js', array( 'bootstrap.min' ), false, true );

  wp_localize_script( 'get-response-add-contact', 'grAddContact', array(
    'ajax_url'		=> admin_url('admin-ajax.php')
  ) );
}
