<?php
/**
 * Title: Sekcja: Najczęściej czytane
 * Slug: sardynkibiznesu-3-0/home-most-read
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: najczęściej czytane, popularne, ranking
 * Description: Sekcja strony głównej: ranking najczęściej czytanych wpisów z całego serwisu (.sb-ranking, ihumbak-site-blocks).
 *
 * The block prints its own H2 „Najczęściej czytane”; the home look (two columns, section
 * heading) comes from the class `sb-ranking--home` (theme CSS ihumbak-most-read.css).
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Najczęściej czytane"},"align":"wide","className":"sb-home-section sb-home-section--most-read","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--most-read">
	<!-- wp:ihumbak/most-read {"count":6,"scope":"all","className":"sb-ranking--home"} /-->
</section>
<!-- /wp:group -->
