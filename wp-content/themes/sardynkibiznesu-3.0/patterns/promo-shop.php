<?php
/**
 * Title: Ramka promocyjna: Sklep
 * Slug: sardynkibiznesu-3-0/promo-shop
 * Categories: call-to-action
 * Keywords: sklep, pakiet RODO, ramka promocyjna, kolumna boczna
 * Description: Ramka promocyjna (core/group, styl accent) z produktem ze sklepu (Pakiet RODO). Używana w Kolumnie bocznej stron.
 *
 * The `is-style-accent` group style is registered by ihumbak-content-elements (#43).
 * The product page link is resolved in PHP (page slug `pakietrodo`), falling back to the
 * Sklep category.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_shop_page = get_page_by_path( 'pakietrodo' );
if ( $sb_shop_page && 'publish' === $sb_shop_page->post_status ) {
	$sb_shop_url = get_permalink( $sb_shop_page );
} else {
	$sb_shop_term = get_term_by( 'slug', 'sklep', 'category' );
	$sb_shop_url  = $sb_shop_term ? get_term_link( $sb_shop_term ) : home_url( '/' );
	if ( is_wp_error( $sb_shop_url ) ) {
		$sb_shop_url = home_url( '/' );
	}
}
?>
<!-- wp:group {"metadata":{"name":"Sklep"},"className":"is-style-accent sb-sidebar__promo","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-accent sb-sidebar__promo">
	<!-- wp:paragraph {"className":"sb-sidebar__eyebrow"} -->
	<p class="sb-sidebar__eyebrow">Sklep</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"className":"sb-sidebar__promo-title"} -->
	<h2 class="wp-block-heading sb-sidebar__promo-title">Pakiet RODO dla małej firmy</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>Polityka, regulamin i klauzule — aktualizowane, gdy zmienia się prawo.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $sb_shop_url ); ?>">Zobacz pakiet</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
