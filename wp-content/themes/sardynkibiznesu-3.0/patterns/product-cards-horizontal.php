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
 * by ihumbak-content-elements; their CSS is in assets/css/blocks/.
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
$sb_image = esc_url( get_theme_file_uri( 'assets/images/product-placeholder.svg' ) );
?>
<!-- wp:group {"layout":{"type":"grid","columnCount":2,"minimumColumnWidth":"22rem"}} -->
<div class="wp-block-group">
<?php foreach ( $sb_cards as $sb_card ) : ?>
	<!-- wp:group {"className":"is-style-product-horizontal","layout":{"type":"default"}} -->
	<div class="wp-block-group is-style-product-horizontal">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="<?php echo $sb_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>" alt=""/></figure>
		<!-- /wp:image -->

		<!-- wp:paragraph {"className":"is-style-<?php echo esc_attr( $sb_card['label_style'] ); ?>"} -->
		<p class="is-style-<?php echo esc_attr( $sb_card['label_style'] ); ?>"><?php echo esc_html( $sb_card['label'] ); ?></p>
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
				<?php if ( $sb_card['outline'] ) : ?>
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html( $sb_card['button'] ); ?></a></div>
				<!-- /wp:button -->
				<?php else : ?>
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html( $sb_card['button'] ); ?></a></div>
				<!-- /wp:button -->
				<?php endif; ?>
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
