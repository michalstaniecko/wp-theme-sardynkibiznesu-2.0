<?php
/**
 * MailerLite client: one call, POST /api/subscribers (upsert; never PUT, which
 * would replace the subscriber's groups). No SDK, no provider abstraction.
 *
 * Double opt-in is an account setting in MailerLite ("Double opt-in for API and
 * integrations"), not a request parameter, so no `status` is sent here.
 * Turning it on is the owner's manual step (issue #37).
 */

namespace Ihumbak\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const API_SUBSCRIBERS = 'https://connect.mailerlite.com/api/subscribers';

/** Pinned API version (X-Version), so API changes cannot silently break the form. */
const API_VERSION = '2026-10-30';

/**
 * Subscribes (or updates) an address in a group.
 *
 * @return string 'ok' (200/201), 'invalid_email' (422) or 'error' (anything else).
 */
function mailerlite_subscribe( string $api_key, string $email, string $name, string $group, string $ip ): string {
	$body = array(
		'email'       => $email,
		'groups'      => array( $group ),
		'opted_in_at' => gmdate( 'Y-m-d H:i:s' ),
		'optin_ip'    => $ip,
		'ip_address'  => $ip,
	);
	if ( '' !== $name ) {
		$body['fields'] = array( 'name' => $name );
	}

	$response = wp_remote_post(
		API_SUBSCRIBERS,
		array(
			'timeout' => 10,
			'headers' => array(
				'Authorization' => 'Bearer ' . $api_key,
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
				'X-Version'     => API_VERSION,
			),
			'body'    => wp_json_encode( $body ),
		)
	);

	if ( is_wp_error( $response ) ) {
		log_error( 'request failed: ' . $response->get_error_message() );
		return 'error';
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( 200 === $code || 201 === $code ) {
		return 'ok';
	}
	if ( 422 === $code ) {
		return 'invalid_email';
	}

	// 401 (key), 404 (group), 429 (rate limit), 5xx: logged, never shown to the reader.
	log_error( sprintf( 'HTTP %d: %s', $code, substr( (string) wp_remote_retrieve_body( $response ), 0, 300 ) ) );
	return 'error';
}

function log_error( string $message ): void {
	error_log( '[ihumbak-newsletter] MailerLite ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
}
