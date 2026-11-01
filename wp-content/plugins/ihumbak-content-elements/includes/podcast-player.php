<?php
/**
 * Odtwarzacz podcastu: the `[spreaker]` shortcode pasted by editors from Spreaker's
 * "share" dialog, rendered with native embed markup, for one episode or a whole show.
 *
 * Own code instead of the wordpress.org `spreaker-shortcode` plugin (1.8.3): that plugin
 * has been unmaintained for about two years and is tested up to WP 6.6 only, while theme
 * 3.0 requires 7.0. All 52 posts use the same small attribute set, so a few dozen lines
 * without dependencies replace it and every environment behaves the same. The tag name
 * stays, so post content is untouched. The switch can be undone by activating the plugin
 * again. Do not run both: the later registration wins (the runbook turns the plugin off).
 *
 * Anything but `type=player` with a valid resource renders nothing: readers never see
 * raw shortcode text or an error message.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\PodcastPlayer;

defined( 'ABSPATH' ) || exit;

const PLAYER_URL = 'https://widget.spreaker.com/player';

/**
 * Shortcode attribute => player URL parameter. Sent only for attributes present in the
 * shortcode; any other attribute is ignored. `episode-image-position` is the one name
 * that differs: this is the parameter the 1.8.3 plugin sends to Spreaker.
 */
const URL_PARAMS = array(
	'theme'                  => 'theme',
	'color'                  => 'color',
	'playlist'               => 'playlist',
	'playlist-continuous'    => 'playlist-continuous',
	'chapters-image'         => 'chapters-image',
	'episode-image-position' => 'episode_image_position',
	'autoplay'               => 'autoplay',
	'live-autoplay'          => 'live-autoplay',
	'hide-logo'              => 'hide-logo',
	'hide-likes'             => 'hide-likes',
	'hide-comments'          => 'hide-comments',
	'hide-sharing'           => 'hide-sharing',
	'hide-download'          => 'hide-download',
);

add_action( 'init', __NAMESPACE__ . '\\register_shortcode' );

/**
 * Registers the tag, which also stops wptexturize from curling the quotes. On `init`,
 * not at load like sibling tags: the external plugin registers at load, so during the
 * switch window this implementation already wins.
 */
function register_shortcode(): void {
	add_shortcode( 'spreaker', __NAMESPACE__ . '\\spreaker_shortcode' );
}

/**
 * Renders the Odtwarzacz podcastu. Spacing comes from the content's flow layout, as for
 * a native YouTube embed.
 *
 * @param array|string $atts Shortcode attributes (an empty string when there are none).
 */
function spreaker_shortcode( $atts ): string {
	$atts = shortcode_atts(
		array(
			'type'     => '',
			'resource' => '',
			'width'    => '',
			'height'   => '',
		) + array_fill_keys( array_keys( URL_PARAMS ), '' ),
		$atts,
		'spreaker'
	);

	if ( 'player' !== $atts['type'] ) {
		return '';
	}

	$url = player_url( $atts );
	if ( '' === $url ) {
		return '';
	}

	return sprintf(
		'<figure class="wp-block-embed is-type-rich is-provider-spreaker wp-block-embed-spreaker sb-podcast-player"><div class="wp-block-embed__wrapper"><iframe src="%s" width="%s" height="%s" title="Odtwarzacz podcastu" loading="lazy" frameborder="0" allow="autoplay"></iframe></div></figure>',
		esc_url( $url ),
		esc_attr( dimension( $atts['width'], '100%' ) ),
		esc_attr( dimension( $atts['height'], '350px' ) )
	);
}

/**
 * Builds the player URL, or returns an empty string when the resource is not valid.
 *
 * @param array $atts Shortcode attributes.
 */
function player_url( array $atts ): string {
	if ( ! preg_match( '/^(episode_id|show_id)=(\d+)$/D', $atts['resource'], $match ) ) {
		return '';
	}

	$params = array( $match[1] => $match[2] );
	foreach ( URL_PARAMS as $attribute => $param ) {
		$value = preg_replace( '/[^a-zA-Z0-9_-]/', '', $atts[ $attribute ] );
		if ( '' !== $value ) {
			$params[ $param ] = $value;
		}
	}

	return PLAYER_URL . '?' . http_build_query( $params );
}

/**
 * A size in pixels or percent; bare digits mean pixels, anything else gets the default.
 *
 * @param string $value   Raw value.
 * @param string $default Fallback.
 */
function dimension( string $value, string $default ): string {
	if ( ! preg_match( '/^(\d+)(px|%)?$/D', $value, $match ) ) {
		return $default;
	}
	return $match[1] . ( $match[2] ?? 'px' );
}
