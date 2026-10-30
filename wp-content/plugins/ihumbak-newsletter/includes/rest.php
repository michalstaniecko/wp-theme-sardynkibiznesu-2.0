<?php
/**
 * JS path of Formularz zapisu: POST /wp-json/ihumbak-newsletter/v1/subscribe.
 *
 * The view script (assets/view.js) sends the form's FormData as it is (signed hidden
 * fields included), so the same process_submission() as the no-JS path decides.
 * Public on purpose: no nonce (pages are cached), the signature stands in for it.
 *
 * Responses (JSON):
 * - 200 {code: "ok", redirect}           → the script goes to the Strona podziękowania;
 * - 422 {code, message}                  → invalid_email / missing_name, shown inline;
 * - 500 {code: "error", message}         → anything else (signature, fill time, limit per IP,
 *                                          MailerLite or settings), shown as the generic message.
 */

namespace Ihumbak\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const REST_NAMESPACE = 'ihumbak-newsletter/v1';
const REST_ROUTE     = '/subscribe';

add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			REST_NAMESPACE,
			REST_ROUTE,
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => __NAMESPACE__ . '\rest_subscribe',
				'permission_callback' => '__return_true',
			)
		);
	}
);

function rest_url_subscribe(): string {
	return rest_url( REST_NAMESPACE . REST_ROUTE );
}

function rest_subscribe( \WP_REST_Request $request ): \WP_REST_Response {
	$result = process_submission( $request->get_body_params(), client_ip() );

	if ( 'ok' === $result['code'] ) {
		$response = new \WP_REST_Response(
			array(
				'code'     => 'ok',
				'redirect' => $result['redirect'],
			),
			200
		);
	} else {
		$messages = messages();
		$code     = isset( $messages[ $result['code'] ] ) ? $result['code'] : 'error';
		$response = new \WP_REST_Response(
			array(
				'code'    => $code,
				'message' => $messages[ $code ],
			),
			'error' === $code ? 500 : 422
		);
	}

	$response->header( 'Cache-Control', 'no-store, private' );
	return $response;
}
