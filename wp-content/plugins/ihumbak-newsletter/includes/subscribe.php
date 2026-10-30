<?php
/**
 * Submission of Formularz zapisu.
 *
 * process_submission() holds the whole server-side logic (antispam, validation,
 * MailerLite) and returns a result; the no-JS handler below turns it into a
 * redirect; the REST endpoint of the JS path (includes/rest.php) turns it into JSON.
 *
 * Antispam without captcha and without a nonce (pages are cached):
 * - honeypot field `ihn_website`: a filled one gets a fake success, no API call;
 * - minimum fill time: the signed render time `ihn_ts` must be MIN_FILL_SECONDS old
 *   (on a cached page the stamp is older, so this only stops instant bots);
 * - limit per IP: RATE_LIMIT submissions per RATE_WINDOW (transient).
 */

namespace Ihumbak\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const MIN_FILL_SECONDS = 2;
const RATE_LIMIT       = 10;
const RATE_WINDOW      = HOUR_IN_SECONDS;

add_action( 'admin_post_nopriv_' . ACTION, __NAMESPACE__ . '\handle_post' );
add_action( 'admin_post_' . ACTION, __NAMESPACE__ . '\handle_post' );

/**
 * @param array  $input Raw form fields (unslashed).
 * @param string $ip    Client IP.
 * @return array{code: string, redirect: string} code 'ok' or an error code from messages();
 *               redirect is the Strona podziękowania for 'ok', '' otherwise.
 */
function process_submission( array $input, string $ip ): array {
	$field  = static fn( string $key ): string => is_scalar( $input[ $key ] ?? null ) ? (string) $input[ $key ] : '';
	$group  = $field( 'ihn_group' );
	$thanks = $field( 'ihn_thankyou' );
	$fields = $field( 'ihn_fields' );
	$ts     = $field( 'ihn_ts' );

	$error = static fn( string $code ): array => array(
		'code'     => $code,
		'redirect' => '',
	);

	if ( ! hash_equals( sign( $group, $thanks, $fields, $ts ), $field( 'ihn_sig' ) ) || ! in_array( $fields, FIELDS, true ) ) {
		return $error( 'error' );
	}

	$success = array(
		'code'     => 'ok',
		'redirect' => wp_validate_redirect( $thanks, thankyou_url() ),
	);
	if ( '' === $thanks ) {
		$success['redirect'] = thankyou_url();
	}

	if ( '' !== $field( 'ihn_website' ) ) {
		return $success; // Honeypot: look successful, subscribe nobody.
	}
	if ( ! ctype_digit( $ts ) || time() - (int) $ts < MIN_FILL_SECONDS ) {
		return $error( 'error' );
	}

	$email = trim( $field( 'email' ) );
	if ( strlen( $email ) > 254 || ! is_email( $email ) ) {
		return $error( 'invalid_email' );
	}
	$name = '';
	if ( 'name-email' === $fields ) {
		$name = mb_substr( trim( sanitize_text_field( $field( 'name' ) ) ), 0, 100 );
		if ( '' === $name ) {
			return $error( 'missing_name' );
		}
	}

	if ( ! within_rate_limit( $ip ) ) {
		return $error( 'error' );
	}

	$group = '' !== $group ? $group : default_group();
	$key   = api_key();
	if ( '' === $key || '' === $group ) {
		log_error( 'not configured: ' . ( '' === $key ? 'API key' : 'group' ) . ' missing (Ustawienia → Newsletter)' );
		return $error( 'error' );
	}

	$code = mailerlite_subscribe( $key, $email, $name, $group, $ip );
	return 'ok' === $code ? $success : $error( $code );
}

/** Counts this submission; false once the IP went over the limit in the window. */
function within_rate_limit( string $ip ): bool {
	$transient = 'ihumbak_newsletter_rl_' . md5( $ip );
	$count     = (int) get_transient( $transient );
	if ( $count >= (int) apply_filters( 'ihumbak_newsletter_rate_limit', RATE_LIMIT ) ) {
		return false;
	}
	set_transient( $transient, $count + 1, RATE_WINDOW );
	return true;
}

/** Client IP. REMOTE_ADDR unless a proxy setup filters it (`ihumbak_newsletter_client_ip`). */
function client_ip(): string {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ip = (string) apply_filters( 'ihumbak_newsletter_client_ip', $ip );
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
}

/** No-JS path: POST to admin-post.php, 303 to Strona podziękowania or back to the form. */
function handle_post(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- no nonce on purpose (cached pages); signed fields instead.
	$input = wp_unslash( $_POST );
	// phpcs:enable
	$result = process_submission( is_array( $input ) ? $input : array(), client_ip() );

	if ( 'ok' === $result['code'] ) {
		wp_safe_redirect( $result['redirect'], 303 );
		exit;
	}

	$form   = is_string( $input['ihn_form'] ?? null ) && preg_match( '/^ihumbak-newsletter-\d+$/', $input['ihn_form'] ) ? $input['ihn_form'] : '';
	$return = is_string( $input['ihn_return'] ?? null ) ? $input['ihn_return'] : '';
	$return = wp_validate_redirect( $return, (string) wp_get_referer() );
	if ( '' === $return ) {
		$return = home_url( '/' );
	}
	$return = remove_query_arg( array( ERROR_PARAM, FORM_PARAM ), strtok( $return, '#' ) );
	$return = add_query_arg(
		array(
			ERROR_PARAM => $result['code'],
			FORM_PARAM  => $form,
		),
		$return
	);

	wp_safe_redirect( $return . ( '' !== $form ? '#' . $form : '' ), 303 );
	exit;
}
