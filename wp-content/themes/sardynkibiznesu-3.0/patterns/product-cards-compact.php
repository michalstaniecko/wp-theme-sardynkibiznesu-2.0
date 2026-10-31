<?php
/**
 * Title: Karty produktu: kompaktowe
 * Slug: sardynkibiznesu-3-0/product-cards-compact
 * Categories: featured, call-to-action
 * Keywords: karta produktu, darmowe narzędzia, kalkulator, materiały, kompaktowe
 * Block Types: core/group
 * Description: Trzy Karty produktu w wariancie kompaktowym (bez obrazu): etykieta, kategoria, tytuł, opis i przycisk. Sekcja „Darmowe narzędzia”, wąskie kolumny.
 *
 * Karta produktu = core blocks only, no block of its own and no price field (a price is
 * plain text in the Plakietka, see product-cards-grid).
 * The compact variant has no image (design system: "bez obrazu użyj --compact"); an
 * image added by the editor is hidden by the style's CSS.
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
		'label'       => 'Za darmo',
		'label_style' => 'badge-success',
		'outline'     => true,
		'category'    => 'Kalkulator',
		'title'       => 'Stawka godzinowa B2B',
		'desc'        => 'Policz, ile naprawdę musisz brać za godzinę, żeby wyjść na swoje po ZUS-ie i podatku.',
		'button'      => 'Policz',
	),
	array(
		'label'       => 'Za darmo',
		'label_style' => 'badge-success',
		'outline'     => true,
		'category'    => 'Kalkulator',
		'title'       => 'VAT netto i brutto',
		'desc'        => 'Przelicz kwoty w obie strony przy stawkach 23, 8 i 5%.',
		'button'      => 'Policz',
	),
	array(
		'label'       => 'Za darmo',
		'label_style' => 'badge-success',
		'outline'     => true,
		'category'    => 'Fiszkurs',
		'title'       => 'Faktura bez błędów',
		'desc'        => 'Dziesięć fiszek o tym, co musi być na fakturze, a czego nie wolno.',
		'button'      => 'Zacznij',
	),
);

echo \Ihumbak\ContentElements\ProductCard\cards_markup( $sb_cards, 'compact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper.
