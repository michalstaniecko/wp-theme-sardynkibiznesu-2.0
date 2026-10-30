<?php
/**
 * Author social links (spec #36, user story 15): the networks read from user
 * meta and the profile fields to fill them in.
 *
 * Meta keys are the ones 2.0 read (`sb_get_author_links()`): `user_url` plus the
 * Yoast contact methods `facebook`, `instagram`, `linkedin`, `youtube`. The
 * profile fields are added here too, so they stay editable without Yoast
 * (same keys, so with Yoast nothing is duplicated).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Networks in display order: meta key => label, inline SVG (24×24 viewBox).
 *
 * @return array<string, array{label: string, icon: string}>
 */
function ihumbak_site_blocks_author_networks(): array {
	return array(
		'user_url'  => array(
			'label' => __( 'Strona www', 'ihumbak-site-blocks' ),
			'icon'  => '<circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
		),
		'facebook'  => array(
			'label' => 'Facebook',
			'icon'  => '<path d="M14 8h3V4h-3c-2.8 0-4.5 1.8-4.5 4.5V11H7v4h2.5v8h4v-8H17l.5-4h-4V8.6c0-.4.2-.6.5-.6z"/>',
		),
		'instagram' => array(
			'label' => 'Instagram',
			'icon'  => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.3"/>',
		),
		'linkedin'  => array(
			'label' => 'LinkedIn',
			'icon'  => '<path d="M3.5 9h4v12h-4zM5.5 2.5a2.3 2.3 0 1 1 0 4.6 2.3 2.3 0 0 1 0-4.6zM10 9h3.8v1.7h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H10z"/>',
		),
		'youtube'   => array(
			'label' => 'YouTube',
			'icon'  => '<path fill-rule="evenodd" d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12a31 31 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.8 31 31 0 0 0-.5-4.8zM9.8 15.3V8.7l5.7 3.3z"/>',
		),
	);
}

/**
 * The author's links that are filled in, valid http(s) URLs, without duplicates
 * (some authors put their Facebook page in both `user_url` and `facebook`;
 * the network wins over „Strona www”).
 *
 * @return array<string, string> meta key => URL, in display order.
 */
function ihumbak_site_blocks_author_links( int $user_id ): array {
	$links = array();
	foreach ( array_keys( ihumbak_site_blocks_author_networks() ) as $key ) {
		$url = esc_url_raw( trim( (string) get_the_author_meta( $key, $user_id ) ), array( 'http', 'https' ) );
		if ( '' !== $url ) {
			$links[ $key ] = $url;
		}
	}
	if ( isset( $links['user_url'] ) && in_array( $links['user_url'], array_slice( $links, 1 ), true ) ) {
		unset( $links['user_url'] );
	}
	return $links;
}

add_filter(
	'user_contactmethods',
	static function ( array $methods ): array {
		foreach ( ihumbak_site_blocks_author_networks() as $key => $network ) {
			if ( 'user_url' !== $key && ! isset( $methods[ $key ] ) ) {
				/* translators: %s: social network name. */
				$methods[ $key ] = sprintf( __( '%s (adres profilu)', 'ihumbak-site-blocks' ), $network['label'] );
			}
		}
		return $methods;
	}
);
