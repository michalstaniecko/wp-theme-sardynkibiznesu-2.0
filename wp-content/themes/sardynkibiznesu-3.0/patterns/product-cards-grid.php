<?php
/**
 * Title: Karty produktu: siatka
 * Slug: sardynkibiznesu-3-0/product-cards-grid
 * Categories: featured, call-to-action
 * Keywords: karta produktu, siatka, produkt, cena, sklep, umowy
 * Block Types: core/group
 * Description: Siatka trzech Kart produktu z obrazem: Plakietka z ceną, tytuł, opis i przycisk, bez kategorii. Na szablon „Strona szeroka”: 3 kolumny na desktopie, 2 na tablecie, 1 na telefonie.
 *
 * Karta produktu = core blocks only, no block of its own and no price field: the price is
 * plain text in the Plakietka (`badge-brand` for a paid product, `badge-success` for
 * „Darmowy”). Markup: cards_markup() in ihumbak-content-elements. The 18rem minimum column
 * width gives 3 columns from 1024 px and 2 on a 768 px tablet.
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
		'label'       => '149 zł',
		'label_style' => 'badge-brand',
		'title'       => 'Pakiet RODO dla małej firmy',
		'desc'        => 'Polityka prywatności, klauzule i rejestry, które wdrożysz w kilka godzin. Zawsze aktualne, z instrukcją krok po kroku, jak dopasować je do swojej firmy.',
		'button'      => 'Zobacz więcej',
	),
	array(
		'label'       => '79 zł',
		'label_style' => 'badge-brand',
		'title'       => 'Wzór umowy o współpracy B2B',
		'desc'        => 'Umowa gotowa do uzupełnienia, z komentarzem do każdego punktu.',
		'button'      => 'Zobacz więcej',
	),
	array(
		'label'       => 'Darmowy',
		'label_style' => 'badge-success',
		'title'       => 'Wzór polityki prywatności',
		'desc'        => 'Prosty, czytelny i zgodny z prawem wzór na stronę, bloga lub sklep.',
		'button'      => 'Pobierz',
		'outline'     => true,
	),
);

echo \Ihumbak\ContentElements\ProductCard\cards_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper.
	$sb_cards,
	'horizontal',
	get_theme_file_uri( 'assets/images/product-placeholder.svg' ),
	array(
		'columns'          => 3,
		'min_column_width' => '18rem',
	)
);
