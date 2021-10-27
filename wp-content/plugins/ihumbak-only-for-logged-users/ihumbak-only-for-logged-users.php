<?php

/**
 * Plugin Name: iHumbak Only For Logged Users
 */

add_action('template_redirect', 'ihumbak_oflu_init');
function ihumbak_oflu_init() {
  if (preg_match('/wp-admin\/|wp-login.*/', $_SERVER['REQUEST_URI'])) return;
  if (!is_super_admin()) wp_redirect('/wp-admin');
}
