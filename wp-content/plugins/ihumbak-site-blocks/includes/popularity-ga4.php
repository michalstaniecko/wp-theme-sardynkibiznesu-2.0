<?php
/**
 * GA4 Data API source of post popularity (spec #36, research #28): page views of the
 * last 30 days per `pagePath`, fetched with a service account.
 *
 * Configuration lives in wp-config.php (provisioning checklist: #40):
 *   define( 'SB_GA4_PROPERTY_ID', '123456789' );            // numeric Property ID, not G-…
 *   define( 'SB_GA4_CREDENTIALS_FILE', '/path/outside/webroot/sb-popularity.json' );
 *   define( 'SB_GA4_HOST_NAME', 'sardynkibiznesu.pl' );     // optional, default: host of home_url()
 *
 * Authentication is the OAuth 2.0 JWT bearer flow signed with `openssl_sign`
 * (RS256), no Composer, no Google SDK. The access token is cached in a transient
 * for 55 minutes; the private key never leaves the credentials file.
 */

namespace Ihumbak\SiteBlocks\Popularity;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page views from the GA4 Data API. `fetch()` returns rows `[ 'path' => string, 'views' => int ]`
 * plus response flags, or a WP_Error when nothing trustworthy was fetched. The API base URL can
 * be pointed elsewhere (e.g. a local fake in tests) with the `ihumbak_site_blocks_ga4_api_url` filter.
 */
final class Ga4_Source {
	const SCOPE       = 'https://www.googleapis.com/auth/analytics.readonly';
	const TOKEN_URI   = 'https://oauth2.googleapis.com/token';
	const API_URL     = 'https://analyticsdata.googleapis.com/v1beta';
	const PAGE_SIZE   = 10000;
	const TOKEN_CACHE = 'ihumbak_site_blocks_ga4_token';
	const MAX_PAGES   = 50;

	public function fetch( int $window_days ): array|WP_Error {
		$config = $this->config();
		if ( is_wp_error( $config ) ) {
			return $config;
		}

		$token = $this->access_token( $config['credentials'] );
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		/** Base URL of the Data API (overridable for tests against a fake server). */
		$url   = apply_filters( 'ihumbak_site_blocks_ga4_api_url', self::API_URL ) . '/properties/' . rawurlencode( $config['property'] ) . ':runReport';
		$rows  = array();
		$flags = array( 'subjectToThresholding' => false, 'dataLossFromOtherRow' => false, 'sampled' => false );

		for ( $page = 0, $offset = 0; $page < self::MAX_PAGES; $page++ ) {
			$report = $this->post_json(
				$url,
				array(
					'dateRanges'      => array( array( 'startDate' => $window_days . 'daysAgo', 'endDate' => 'yesterday' ) ),
					'dimensions'      => array( array( 'name' => 'hostName' ), array( 'name' => 'pagePath' ) ),
					'metrics'         => array( array( 'name' => 'screenPageViews' ) ),
					'dimensionFilter' => array(
						'filter' => array(
							'fieldName'    => 'hostName',
							'stringFilter' => array( 'value' => $config['host'] ),
						),
					),
					'orderBys'        => array( array( 'metric' => array( 'metricName' => 'screenPageViews' ), 'desc' => true ) ),
					'limit'           => (string) self::PAGE_SIZE,
					'offset'          => (string) $offset,
				),
				array( 'Authorization' => 'Bearer ' . $token )
			);
			if ( is_wp_error( $report ) ) {
				if ( 401 === ( $report->get_error_data()['status'] ?? null ) ) {
					delete_transient( self::TOKEN_CACHE ); // Revoked or expired early: next run signs a new JWT.
				}
				return $report;
			}

			$meta                           = $report['metadata'] ?? array();
			$flags['subjectToThresholding'] = $flags['subjectToThresholding'] || ! empty( $meta['subjectToThresholding'] );
			$flags['dataLossFromOtherRow']  = $flags['dataLossFromOtherRow'] || ! empty( $meta['dataLossFromOtherRow'] );
			$flags['sampled']               = $flags['sampled'] || ! empty( $meta['samplingMetadatas'] );

			$batch = $report['rows'] ?? array();
			foreach ( $batch as $row ) {
				$rows[] = array(
					'path'  => (string) ( $row['dimensionValues'][1]['value'] ?? '' ),
					'views' => (int) ( $row['metricValues'][0]['value'] ?? 0 ),
				);
			}

			// GA4 pages by `limit`/`offset`; it may return fewer rows than asked per page.
			$offset += count( $batch );
			if ( ! $batch || $offset >= (int) ( $report['rowCount'] ?? 0 ) ) {
				break;
			}
		}

		return array( 'rows' => $rows, 'flags' => $flags );
	}

	/**
	 * @return array{property: string, host: string, credentials: array<string, string>}|WP_Error
	 */
	private function config(): array|WP_Error {
		if ( ! defined( 'SB_GA4_PROPERTY_ID' ) || '' === (string) SB_GA4_PROPERTY_ID ) {
			return new WP_Error( 'sb_ga4_config', 'Brak stałej SB_GA4_PROPERTY_ID w wp-config.php.' );
		}
		if ( ! defined( 'SB_GA4_CREDENTIALS_FILE' ) || '' === (string) SB_GA4_CREDENTIALS_FILE ) {
			return new WP_Error( 'sb_ga4_config', 'Brak stałej SB_GA4_CREDENTIALS_FILE w wp-config.php.' );
		}

		$file = (string) SB_GA4_CREDENTIALS_FILE;
		$json = is_readable( $file ) ? file_get_contents( $file ) : false; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file outside webroot.
		if ( false === $json ) {
			return new WP_Error( 'sb_ga4_config', sprintf( 'Nie można odczytać pliku danych logowania GA4: %s.', $file ) );
		}
		$credentials = json_decode( $json, true );
		if ( empty( $credentials['client_email'] ) || empty( $credentials['private_key'] ) ) {
			return new WP_Error( 'sb_ga4_config', sprintf( 'Plik %s nie jest kluczem JSON konta usługi (brak client_email lub private_key).', $file ) );
		}

		$host = defined( 'SB_GA4_HOST_NAME' ) && SB_GA4_HOST_NAME ? (string) SB_GA4_HOST_NAME : (string) wp_parse_url( home_url(), PHP_URL_HOST );

		return array(
			'property'    => (string) SB_GA4_PROPERTY_ID,
			'host'        => $host,
			'credentials' => $credentials,
		);
	}

	/**
	 * @param array<string, string> $credentials Decoded service account key.
	 */
	private function access_token( array $credentials ): string|WP_Error {
		$cached = get_transient( self::TOKEN_CACHE );
		if ( is_array( $cached ) && ( $cached['client'] ?? '' ) === $credentials['client_email'] && ! empty( $cached['token'] ) ) {
			return $cached['token'];
		}

		$token_uri = $credentials['token_uri'] ?? self::TOKEN_URI;
		$jwt       = $this->jwt( $credentials, $token_uri );
		if ( is_wp_error( $jwt ) ) {
			return $jwt;
		}

		$response = wp_remote_post(
			$token_uri,
			array(
				'timeout' => 20,
				'body'    => array(
					'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
					'assertion'  => $jwt,
				),
			)
		);
		$data     = $this->decode( $response, 'token' );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		if ( empty( $data['access_token'] ) ) {
			return new WP_Error( 'sb_ga4_token', 'Odpowiedź serwera tokenów nie zawiera access_token.' );
		}

		$ttl = max( 60, min( 55 * MINUTE_IN_SECONDS, (int) ( $data['expires_in'] ?? 3600 ) - 5 * MINUTE_IN_SECONDS ) );
		set_transient( self::TOKEN_CACHE, array( 'client' => $credentials['client_email'], 'token' => $data['access_token'] ), $ttl );

		return $data['access_token'];
	}

	/**
	 * Signed JWT assertion for the token endpoint (RS256, valid 1 h).
	 *
	 * @param array<string, string> $credentials Decoded service account key.
	 */
	private function jwt( array $credentials, string $audience ): string|WP_Error {
		$key = openssl_pkey_get_private( $credentials['private_key'] );
		if ( false === $key ) {
			return new WP_Error( 'sb_ga4_key', 'Nie można wczytać klucza prywatnego konta usługi.' );
		}

		$now   = time();
		$parts = array(
			self::base64url( (string) wp_json_encode( array( 'alg' => 'RS256', 'typ' => 'JWT' ) ) ),
			self::base64url(
				(string) wp_json_encode(
					array(
						'iss'   => $credentials['client_email'],
						'scope' => self::SCOPE,
						'aud'   => $audience,
						'iat'   => $now,
						'exp'   => $now + HOUR_IN_SECONDS,
					)
				)
			),
		);

		$signature = '';
		if ( ! openssl_sign( implode( '.', $parts ), $signature, $key, OPENSSL_ALGO_SHA256 ) ) {
			return new WP_Error( 'sb_ga4_key', 'Podpisanie JWT kluczem konta usługi nie powiodło się.' );
		}
		$parts[] = self::base64url( $signature );

		return implode( '.', $parts );
	}

	/**
	 * @param array<string, mixed>  $body    Request body.
	 * @param array<string, string> $headers Extra headers.
	 * @return array<string, mixed>|WP_Error
	 */
	private function post_json( string $url, array $body, array $headers ): array|WP_Error {
		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 30,
				'headers' => $headers + array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( $body ),
			)
		);
		return $this->decode( $response, 'runReport' );
	}

	/**
	 * @param array<string, mixed>|WP_Error $response wp_remote_* result.
	 * @return array<string, mixed>|WP_Error
	 */
	private function decode( array|WP_Error $response, string $what ): array|WP_Error {
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'sb_ga4_http', sprintf( '%s: %s', $what, $response->get_error_message() ) );
		}
		$status = (int) wp_remote_retrieve_response_code( $response );
		$data   = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $status < 200 || $status >= 300 ) {
			$message = $data['error']['message'] ?? $data['error_description'] ?? $data['error'] ?? '';
			return new WP_Error(
				'sb_ga4_http',
				sprintf( '%s: HTTP %d%s', $what, $status, is_string( $message ) && $message ? ': ' . $message : '' ),
				array( 'status' => $status )
			);
		}
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'sb_ga4_http', sprintf( '%s: odpowiedź nie jest JSON-em.', $what ) );
		}
		return $data;
	}

	private static function base64url( string $data ): string {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- JWT encoding.
	}
}
