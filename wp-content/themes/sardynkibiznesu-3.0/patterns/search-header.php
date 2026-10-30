<?php
/**
 * Title: Nagłówek wyszukiwarki
 * Slug: sardynkibiznesu-3-0/search-header
 * Categories: text
 * Inserter: no
 * Description: H1, formularz wyszukiwania i liczba wyników; na stronie 404 H1 i komunikat zamiast liczby wyników.
 *
 * Shared by templates search and 404 (404 is assembled like an empty search).
 * The pattern file is included at render time, so it picks the variant for the
 * current view and prints the result count of the main query.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

if ( is_404() ) : ?>
<!-- wp:heading {"level":1,"className":"sb-search__title"} -->
<h1 class="wp-block-heading sb-search__title">Nie znaleźliśmy tej strony</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sb-search__summary"} -->
<p class="sb-search__summary">Adres mógł się zmienić albo strona zniknęła. Wyszukaj to, czego potrzebujesz, albo zacznij od jednej z kategorii.</p>
<!-- /wp:paragraph -->
<?php else : ?>
<!-- wp:heading {"level":1,"className":"sb-search__title"} -->
<h1 class="wp-block-heading sb-search__title">Szukaj na blogu</h1>
<!-- /wp:heading -->
<?php endif; ?>

<!-- wp:search {"label":"Czego szukasz?","showLabel":false,"placeholder":"Czego szukasz? Np. RODO, faktura, B2B…","buttonText":"Szukaj","className":"sb-search__form"} /-->
<?php
if ( is_search() ) :
	$sb_summary = \SardynkiBiznesu\Theme\search_summary_html( (int) $GLOBALS['wp_query']->found_posts, get_search_query( false ) );
	?>

<!-- wp:paragraph {"className":"sb-search__summary"} -->
<p class="sb-search__summary"><?php echo $sb_summary; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in search_summary_html(). ?></p>
<!-- /wp:paragraph -->
	<?php
endif;
