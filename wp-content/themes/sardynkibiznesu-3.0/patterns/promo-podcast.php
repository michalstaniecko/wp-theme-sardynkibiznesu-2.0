<?php
/**
 * Title: Ramka promocyjna: Podcast
 * Slug: sardynkibiznesu-3-0/promo-podcast
 * Categories: call-to-action
 * Keywords: podcast, ramka promocyjna, kolumna boczna
 * Description: Ramka promocyjna (core/group, styl accent) zapraszająca do odcinków z kategorii Podcasty. Używana w Kolumnach bocznych.
 *
 * The `is-style-accent` group style is registered by ihumbak-content-elements (#43).
 * The category link is resolved in PHP, so it follows the permalink settings.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_podcast_term = get_term_by( 'slug', 'podcasty', 'category' );
$sb_podcast_url  = $sb_podcast_term ? get_term_link( $sb_podcast_term ) : home_url( '/' );
if ( is_wp_error( $sb_podcast_url ) ) {
	$sb_podcast_url = home_url( '/' );
}
?>
<!-- wp:group {"className":"is-style-accent sb-promo","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-accent sb-promo">
	<!-- wp:paragraph {"className":"sb-promo__overline"} -->
	<p class="sb-promo__overline">Podcast</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"fontSize":"h4"} -->
	<h2 class="wp-block-heading has-h-4-font-size">Sardynki Biznesu do słuchania</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"fontSize":"body-sm"} -->
	<p class="has-body-sm-font-size">Rozmowy o prowadzeniu firmy, podatkach i ZUS-ie, bez żargonu. Nowy odcinek do drogi albo na spacer.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $sb_podcast_url ); ?>">Posłuchaj odcinków</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
