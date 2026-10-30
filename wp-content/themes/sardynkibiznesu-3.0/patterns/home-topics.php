<?php
/**
 * Title: Sekcja: O czym piszemy
 * Slug: sardynkibiznesu-3-0/home-topics
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: kategorie, o czym piszemy, tematy
 * Description: Sekcja strony głównej: kafle kategorii głównych z nazwą, opisem i liczbą wpisów.
 *
 * core/terms-query (top-level categories by post count; the editor can pick categories in
 * the block's settings) with the tile style of the category tiles (.sb-category-tile).
 * The count with the Polish noun is ihumbak/term-post-count (ihumbak-site-blocks).
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"O czym piszemy"},"align":"wide","className":"sb-home-section sb-home-section--topics","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--topics">
	<!-- wp:group {"className":"sb-home-section__header","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-home-section__header">
		<!-- wp:heading {"className":"sb-home-section__title"} -->
		<h2 class="wp-block-heading sb-home-section__title">O czym piszemy</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:terms-query {"termQuery":{"perPage":6,"taxonomy":"category","order":"desc","orderBy":"count","include":[],"hideEmpty":true,"showNested":false,"inherit":false},"className":"sb-home-topics"} -->
	<div class="wp-block-terms-query sb-home-topics">
		<!-- wp:term-template {"className":"sb-category-tiles__grid"} -->
			<!-- wp:group {"className":"sb-category-tile","layout":{"type":"default"}} -->
			<div class="wp-block-group sb-category-tile">
				<!-- wp:term-name {"level":3,"isLink":true,"className":"sb-category-tile__name"} /-->

				<!-- wp:term-description {"className":"sb-category-tile__desc"} /-->

				<!-- wp:ihumbak/term-post-count {"className":"sb-category-tile__count"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:term-template -->
	</div>
	<!-- /wp:terms-query -->
</section>
<!-- /wp:group -->
