<?php
/**
 * ihumbak/newsletter-form (also renders [sardynki_getresponse_form], see includes/form.php).
 *
 * @var array  $attributes Block attributes.
 * @var string $content    Inner blocks: title and text.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo \Ihumbak\Newsletter\render_form( $attributes, (string) $content ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in render_form().
