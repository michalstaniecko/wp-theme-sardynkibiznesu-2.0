<?php
/**
 * Title: Stopka
 * Slug: sardynkibiznesu-3-0/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 * Description: Logo, opis serwisu, trzy kolumny linków i pasek dolny z przyciskiem ustawień cookies (CookieYes).
 *
 * The footer part embeds this pattern so that theme asset URLs and the year are
 * resolved in PHP. Once edited in the Site Editor, the part is stored in the
 * database with these values frozen.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_logo_url = get_theme_file_uri( 'assets/images/logo-sardynkibiznesu.png' );
$sb_home_url = home_url( '/' );
?>
<!-- wp:group {"className":"sb-footer__inner"} -->
<div class="wp-block-group sb-footer__inner">
	<!-- wp:group {"className":"sb-footer__grid"} -->
	<div class="wp-block-group sb-footer__grid">
		<!-- wp:group {"className":"sb-footer__brand"} -->
		<div class="wp-block-group sb-footer__brand">
			<!-- wp:image {"lightbox":{"enabled":false},"width":"182px","height":"32px","sizeSlug":"full","linkDestination":"custom","className":"sb-footer__logo"} -->
			<figure class="wp-block-image size-full is-resized sb-footer__logo"><a href="<?php echo esc_url( $sb_home_url ); ?>"><img src="<?php echo esc_url( $sb_logo_url ); ?>" alt="Sardynki Biznesu — strona główna" style="width:182px;height:32px"/></a></figure>
			<!-- /wp:image -->

			<!-- wp:paragraph {"className":"sb-footer__tagline"} -->
			<p class="sb-footer__tagline">Jak usprawnić małą firmę. Blog i podcast o marketingu, prawie i narzędziach — po ludzkiemu.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"className":"sb-footer__cta"} -->
			<div class="wp-block-buttons sb-footer__cta"><!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/zapis-na-newsletter/">Zapisz się na newsletter</a></div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"sb-footer__column"} -->
		<div class="wp-block-group sb-footer__column">
			<!-- wp:heading {"className":"sb-footer__heading"} -->
			<h2 class="wp-block-heading sb-footer__heading">Sklep</h2>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"sb-footer__links"} -->
			<ul class="wp-block-list sb-footer__links"><!-- wp:list-item -->
			<li><a href="/pakietrodo/">Pakiet RODO</a></li>
			<!-- /wp:list-item -->

			<!-- wp:list-item -->
			<li><a href="/regulamin-sklepu/">Regulamin sklepu</a></li>
			<!-- /wp:list-item -->

			<!-- wp:list-item -->
			<li><a href="/umowy/">Wzory umów</a></li>
			<!-- /wp:list-item --></ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"sb-footer__column"} -->
		<div class="wp-block-group sb-footer__column">
			<!-- wp:heading {"className":"sb-footer__heading"} -->
			<h2 class="wp-block-heading sb-footer__heading">Za darmo</h2>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"sb-footer__links"} -->
			<ul class="wp-block-list sb-footer__links"><!-- wp:list-item -->
			<li><a href="/polityka-prywatnosci-dla-strony-bloga-pobierz-wzor/">Polityka prywatności — wzór</a></li>
			<!-- /wp:list-item -->

			<!-- wp:list-item -->
			<li><a href="/kalkulator-b2b-wyceny-uslugi-i-stawki-godzinowej/">Kalkulatory</a></li>
			<!-- /wp:list-item -->

			<!-- wp:list-item -->
			<li><a href="/fiszkurs-regulaminy/">Fiszkursy</a></li>
			<!-- /wp:list-item -->

			<!-- wp:list-item -->
			<li><a href="/strefa-sardynek/">Strefa Sardynek</a></li>
			<!-- /wp:list-item --></ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"sb-footer__column"} -->
		<div class="wp-block-group sb-footer__column">
			<!-- wp:heading {"className":"sb-footer__heading"} -->
			<h2 class="wp-block-heading sb-footer__heading">O nas</h2>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"sb-footer__links"} -->
			<ul class="wp-block-list sb-footer__links"><!-- wp:list-item -->
			<li><a href="/blogu/">O blogu</a></li>
			<!-- /wp:list-item -->

			<!-- wp:list-item -->
			<li><a href="/kategorie/podcasty/">Podcast</a></li>
			<!-- /wp:list-item -->

			<!-- wp:list-item -->
			<li><a href="/kontakt/">Kontakt</a></li>
			<!-- /wp:list-item --></ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"sb-footer__bottom"} -->
	<div class="wp-block-group sb-footer__bottom">
		<!-- wp:paragraph -->
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Sardynki Biznesu</p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph -->
		<p><a href="/polityka-prywatnosci/">Polityka prywatności</a> · <a href="/regulamin-strony/">Regulamin</a></p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"className":"sb-footer__cookies"} -->
		<div class="wp-block-buttons sb-footer__cookies"><!-- wp:button {"tagName":"button","className":"cky-banner-element"} -->
		<div class="wp-block-button cky-banner-element"><button type="button" class="wp-block-button__link wp-element-button">Ustawienia cookies</button></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
