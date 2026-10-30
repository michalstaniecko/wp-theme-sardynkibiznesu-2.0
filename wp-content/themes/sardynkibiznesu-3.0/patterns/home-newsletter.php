<?php
/**
 * Title: Sekcja: Newsletter
 * Slug: sardynkibiznesu-3-0/home-newsletter
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: newsletter, zapis, formularz zapisu
 * Description: Sekcja strony głównej: Formularz zapisu w układzie „section” (ihumbak-newsletter), z edytowalnym nagłówkiem i tekstem.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Newsletter"},"align":"wide","className":"sb-home-section sb-home-section--newsletter","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--newsletter">
	<!-- wp:ihumbak/newsletter-form {"layout":"section","fields":"name-email"} -->
		<!-- wp:heading -->
		<h2 class="wp-block-heading">Zapisz się na newsletter</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p>Praktyczne porady dla małych firm: marketing, prawo i pieniądze. Raz na jakiś czas, prosto na Twoją skrzynkę.</p>
		<!-- /wp:paragraph -->
	<!-- /wp:ihumbak/newsletter-form -->
</section>
<!-- /wp:group -->
