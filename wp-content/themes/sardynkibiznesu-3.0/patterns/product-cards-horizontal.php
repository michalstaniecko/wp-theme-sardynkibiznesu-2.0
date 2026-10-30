<?php
/**
 * Title: Karty produktu: poziome
 * Slug: sardynkibiznesu-3-0/product-cards-horizontal
 * Categories: featured, call-to-action
 * Keywords: karta produktu, polecamy, produkt, sklep, poziome
 * Block Types: core/group
 * Description: Dwie Karty produktu w wariancie poziomym (obraz po lewej): etykieta, kategoria, tytuł, opis i przycisk. Sekcja „Polecamy na start”, strony /start i /umowy.
 *
 * Karta produktu = core blocks only, no block of its own and no prices (spec #36).
 * The `product-horizontal` group style and the Plakietka paragraph styles are registered
 * by ihumbak-content-elements; their CSS is in assets/css/blocks/. Markup: product_cards_markup()
 * in functions.php (shared with the Sekcje strony głównej).
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

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

echo \SardynkiBiznesu\Theme\product_cards_markup( $sb_cards, 'horizontal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper.
