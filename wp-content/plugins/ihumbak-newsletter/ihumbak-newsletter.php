<?php
/**
 * Plugin Name:       iHumbak Newsletter
 * Description:       Formularz zapisu do Newslettera (MailerLite): blok ihumbak/newsletter-form i stary shortcode [sardynki_getresponse_form] jako alias. Działa bez JavaScriptu (POST do admin-post.php), po zapisie przekierowuje na Stronę podziękowania. Ustawienia → Newsletter.
 * Version:           1.0.0
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            iHumbak
 * Text Domain:       ihumbak-newsletter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_NEWSLETTER_FILE = __FILE__;

require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/mailerlite.php';
require_once __DIR__ . '/includes/form.php';
require_once __DIR__ . '/includes/subscribe.php';
require_once __DIR__ . '/includes/activation.php';

register_activation_hook( __FILE__, 'Ihumbak\Newsletter\activate' );
