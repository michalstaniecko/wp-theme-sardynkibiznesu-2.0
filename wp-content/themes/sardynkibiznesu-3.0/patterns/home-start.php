<?php
/**
 * Title: Sekcja: Polecamy na start
 * Slug: sardynkibiznesu-3-0/home-start
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: polecamy, start, produkty, sklep, karta produktu
 * Description: Sekcja strony głównej: dwie Karty produktu w wariancie poziomym (Pakiet RODO, wzory umów).
 *
 * Starting content only: the editor changes cards, texts and links in the page. Links are
 * resolved from page paths, the images from the pages' featured images.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

use function SardynkiBiznesu\Theme\page_url;

$sb_image = static function ( string $path ): ?string {
	$page = get_page_by_path( $path );
	$url  = $page ? get_the_post_thumbnail_url( $page, 'large' ) : false;
	return $url ? $url : null;
};

$sb_cards = array(
	array(
		'label'       => 'Bestseller',
		'label_style' => 'badge-brand',
		'category'    => 'Sklep',
		'title'       => 'Pakiet RODO dla małej firmy',
		'desc'        => 'Polityka prywatności, klauzule i rejestry, które wdrożysz w kilka godzin. Zawsze aktualne.',
		'button'      => 'Zobacz pakiet',
		'url'         => page_url( 'pakietrodo' ),
		'image'       => $sb_image( 'pakietrodo' ),
	),
	array(
		'label'       => 'Polecamy',
		'label_style' => 'badge',
		'category'    => 'Sklep',
		'title'       => 'Gotowe wzory umów dla małej firmy',
		'desc'        => 'Umowy z klientami, podwykonawcami i współpracownikami, z instrukcją wypełnienia.',
		'button'      => 'Zobacz wzory',
		'outline'     => true,
		'url'         => page_url( 'umowy' ),
		'image'       => $sb_image( 'umowy' ),
	),
);
$sb_cards = array_map( static fn ( array $card ): array => array_filter( $card, static fn ( $value ): bool => null !== $value ), $sb_cards );
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Polecamy na start"},"align":"wide","className":"sb-home-section sb-home-section--start","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--start">
	<!-- wp:group {"className":"sb-home-section__header","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-home-section__header">
		<!-- wp:heading {"className":"sb-home-section__title"} -->
		<h2 class="wp-block-heading sb-home-section__title">Polecamy na start</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"sb-home-section__more"} -->
		<p class="sb-home-section__more"><a href="<?php echo esc_url( page_url( 'start' ) ); ?>">Wszystkie produkty</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<?php echo \SardynkiBiznesu\Theme\product_cards_markup( $sb_cards, 'horizontal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
</section>
<!-- /wp:group -->
