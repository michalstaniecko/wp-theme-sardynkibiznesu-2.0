<?php
/**
 * Podcast episode meta: episode number and duration of a post (an episode in the
 * Podcasty category). Shown on the front page by Block Bindings (`core/post-meta`), so
 * both keys are public (no leading underscore), scalar strings and in the REST API.
 * Strings, not integers: the editor only offers string fields for binding to text
 * (paragraph content), and both values are displayed as they are typed.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\Podcast;

defined( 'ABSPATH' ) || exit;

/** Episode number, digits only, e.g. "112". */
const META_EPISODE = 'sb_podcast_episode';

/** Duration as displayed, e.g. "38 min". */
const META_DURATION = 'sb_podcast_duration';

add_action( 'init', __NAMESPACE__ . '\\register_meta' );

/**
 * Registers both meta keys on posts.
 */
function register_meta(): void {
	$common = array(
		'type'          => 'string',
		'single'        => true,
		'default'       => '',
		'show_in_rest'  => true,
		'auth_callback' => static fn ( $allowed, $meta_key, $post_id ): bool => current_user_can( 'edit_post', $post_id ),
	);

	register_post_meta(
		'post',
		META_EPISODE,
		$common + array(
			'label'             => 'Numer odcinka',
			'description'       => 'Numer odcinka podcastu, same cyfry, np. 112.',
			'sanitize_callback' => __NAMESPACE__ . '\\sanitize_episode',
		)
	);

	register_post_meta(
		'post',
		META_DURATION,
		$common + array(
			'label'             => 'Czas trwania',
			'description'       => 'Czas trwania odcinka tak, jak ma się wyświetlić, np. 38 min.',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
}

/**
 * Keeps only the digits of an episode number ("#112" → "112").
 *
 * @param mixed $value Raw value.
 */
function sanitize_episode( $value ): string {
	return preg_replace( '/\D+/', '', (string) $value );
}
