<?php
/**
 * Title: Karty produktu: poziome
 * Slug: sardynkibiznesu-3-0/product-cards-horizontal
 * Categories: featured, call-to-action
 * Keywords: karta produktu, polecamy, produkt, sklep, poziome
 * Block Types: core/group
 * Description: Dwie Karty produktu w wariancie poziomym (obraz po lewej): Plakietka, kategoria, tytuł, opis i przycisk. Sekcja „Polecamy na start”.
 *
 * Karta produktu rules: includes/product-card.php in ihumbak-content-elements.
 * The `product-horizontal` group style and the Plakietka paragraph styles are registered
 * by ihumbak-content-elements; their CSS is in assets/css/blocks/. Markup: cards_markup() in
 * ihumbak-content-elements (shared with the Sekcje strony głównej).
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

// Karta produktu markup comes from ihumbak-content-elements (ADR-0001); without it the pattern is empty.
if ( ! function_exists( 'Ihumbak\\ContentElements\\ProductCard\\cards_markup' ) ) {
	return;
}

$sb_cards = array(
	array(
		'label'       => 'Bestseller',
		'label_style' => 'badge-brand',
		'category'    => 'Sklep',
		'title'       => 'Pakiet RODO dla małej firmy',
		'desc'        => 'Polityka prywatności, regulamin i klauzule do formularzy. Gotowe do wklejenia, zawsze aktualne.',
		'button'      => 'Zobacz więcej',
		'outline'     => false,
	),
	array(
		'label'       => 'Za darmo',
		'label_style' => 'badge-success',
		'category'    => 'Materiały',
		'title'       => 'Wzór polityki prywatności',
		'desc'        => 'Prosty, czytelny i zgodny z prawem wzór na stronę, bloga lub sklep.',
		'button'      => 'Pobierz',
		'outline'     => true,
	),
);

echo \Ihumbak\ContentElements\ProductCard\cards_markup( $sb_cards, 'horizontal', get_theme_file_uri( 'assets/images/product-placeholder.svg' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper.
