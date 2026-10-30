<?php
/**
 * Title: Ramka promocyjna: Podcast
 * Slug: sardynkibiznesu-3-0/promo-podcast
 * Categories: call-to-action
 * Keywords: podcast, ramka promocyjna, kolumna boczna
 * Description: Ramka promocyjna (core/group, styl accent) zapraszająca do odcinków z kategorii Podcasty. Używana w Kolumnach bocznych.
 *
 * The `is-style-accent` group style is registered by ihumbak-content-elements (#43). Same
 * markup as the other sidebar frames (promo-podcast-inverse, promo-shop): the
 * `.sb-sidebar__promo` / `__eyebrow` / `__promo-title` classes are styled in style.css.
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
<!-- wp:group {"metadata":{"name":"Podcast"},"className":"is-style-accent sb-sidebar__promo","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-accent sb-sidebar__promo">
	<!-- wp:paragraph {"className":"sb-sidebar__eyebrow"} -->
	<p class="sb-sidebar__eyebrow">Podcast</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"className":"sb-sidebar__promo-title"} -->
	<h2 class="wp-block-heading sb-sidebar__promo-title">Sardynki Biznesu do słuchania</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>Rozmowy o prowadzeniu firmy, podatkach i ZUS-ie, bez żargonu. Nowy odcinek do drogi albo na spacer.</p>
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
