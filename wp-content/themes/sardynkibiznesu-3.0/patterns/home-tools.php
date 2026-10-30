<?php
/**
 * Title: Sekcja: Darmowe narzędzia
 * Slug: sardynkibiznesu-3-0/home-tools
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: darmowe narzędzia, kalkulator, wzór, karta produktu
 * Description: Sekcja strony głównej: trzy Karty produktu w wariancie kompaktowym (kalkulatory, darmowy wzór polityki prywatności).
 *
 * Starting content only: the editor changes cards, texts and links in the page.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

// Karta produktu markup comes from ihumbak-content-elements (ADR-0001); without it the pattern is empty.
if ( ! function_exists( 'Ihumbak\\ContentElements\\ProductCard\\cards_markup' ) ) {
	return;
}

use function Ihumbak\ContentElements\ProductCard\page_url;

$sb_cards = array(
	array(
		'label'       => 'Za darmo',
		'label_style' => 'badge-success',
		'category'    => 'Kalkulator',
		'title'       => 'Kalkulator B2B i stawki godzinowej',
		'desc'        => 'Policz, ile musisz brać za godzinę, żeby wyjść na swoje po ZUS-ie i podatku.',
		'button'      => 'Policz',
		'outline'     => true,
		'url'         => page_url( 'kalkulator-b2b-wyceny-uslugi-i-stawki-godzinowej' ),
	),
	array(
		'label'       => 'Za darmo',
		'label_style' => 'badge-success',
		'category'    => 'Kalkulator',
		'title'       => 'Kalkulator VAT',
		'desc'        => 'Przelicz kwoty netto i brutto w obie strony.',
		'button'      => 'Policz',
		'outline'     => true,
		'url'         => page_url( 'kalkulator-vat' ),
	),
	array(
		'label'       => 'Za darmo',
		'label_style' => 'badge-success',
		'category'    => 'Wzór',
		'title'       => 'Wzór polityki prywatności',
		'desc'        => 'Prosty i czytelny wzór na stronę, bloga lub sklep, z instrukcją wdrożenia.',
		'button'      => 'Pobierz',
		'outline'     => true,
		'url'         => page_url( 'polityka-prywatnosci-dla-strony-bloga-pobierz-wzor' ),
	),
);
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Darmowe narzędzia"},"align":"wide","className":"sb-home-section sb-home-section--tools","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--tools">
	<!-- wp:group {"className":"sb-home-section__header","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-home-section__header">
		<!-- wp:heading {"className":"sb-home-section__title"} -->
		<h2 class="wp-block-heading sb-home-section__title">Darmowe narzędzia</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<?php echo \Ihumbak\ContentElements\ProductCard\cards_markup( $sb_cards, 'compact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
</section>
<!-- /wp:group -->
