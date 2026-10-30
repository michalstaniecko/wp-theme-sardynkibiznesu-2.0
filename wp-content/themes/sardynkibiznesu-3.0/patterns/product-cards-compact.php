<?php
/**
 * Title: Karty produktu: kompaktowe
 * Slug: sardynkibiznesu-3-0/product-cards-compact
 * Categories: featured, call-to-action
 * Keywords: karta produktu, darmowe narzędzia, kalkulator, materiały, kompaktowe
 * Block Types: core/group
 * Description: Trzy Karty produktu w wariancie kompaktowym (bez obrazu): etykieta, kategoria, tytuł, opis i przycisk. Sekcja „Darmowe narzędzia”, wąskie kolumny.
 *
 * Karta produktu = core blocks only, no block of its own and no prices (spec #36).
 * The compact variant has no image (design system: "bez obrazu użyj --compact"); an
 * image added by the editor is hidden by the style's CSS.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_cards = array(
	array(
		'category' => 'Kalkulator',
		'title'    => 'Stawka godzinowa B2B',
		'desc'     => 'Policz, ile naprawdę musisz brać za godzinę, żeby wyjść na swoje po ZUS-ie i podatku.',
		'button'   => 'Policz',
	),
	array(
		'category' => 'Kalkulator',
		'title'    => 'VAT netto i brutto',
		'desc'     => 'Przelicz kwoty w obie strony przy stawkach 23, 8 i 5%.',
		'button'   => 'Policz',
	),
	array(
		'category' => 'Fiszkurs',
		'title'    => 'Faktura bez błędów',
		'desc'     => 'Dziesięć fiszek o tym, co musi być na fakturze, a czego nie wolno.',
		'button'   => 'Zacznij',
	),
);
?>
<!-- wp:group {"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"16rem"}} -->
<div class="wp-block-group">
<?php foreach ( $sb_cards as $sb_card ) : ?>
	<!-- wp:group {"className":"is-style-product-compact","layout":{"type":"default"}} -->
	<div class="wp-block-group is-style-product-compact">
		<!-- wp:paragraph {"className":"is-style-badge-success"} -->
		<p class="is-style-badge-success">Za darmo</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph -->
			<p><a href="#"><?php echo esc_html( $sb_card['category'] ); ?></a></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><a href="#"><?php echo esc_html( $sb_card['title'] ); ?></a></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html( $sb_card['desc'] ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html( $sb_card['button'] ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
