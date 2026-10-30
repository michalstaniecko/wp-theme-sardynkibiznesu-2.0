<?php
/**
 * Title: Kafle kategorii
 * Slug: sardynkibiznesu-3-0/category-tiles
 * Categories: text
 * Inserter: no
 * Description: „Albo zacznij od kategorii”: cztery największe kategorie główne jako kafle z nazwą i opisem.
 *
 * Used by the empty search view and the 404 template. core/terms-query lists
 * top-level categories by post count, so the tiles follow the content without
 * hard-coded term ids. Tile names are H3 under the section's H2.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"metadata":{"name":"Kafle kategorii"},"className":"sb-category-tiles","layout":{"type":"default"}} -->
<div class="wp-block-group sb-category-tiles">
	<!-- wp:heading {"className":"sb-sidebar__heading"} -->
	<h2 class="wp-block-heading sb-sidebar__heading">Albo zacznij od kategorii</h2>
	<!-- /wp:heading -->

	<!-- wp:terms-query {"termQuery":{"perPage":4,"taxonomy":"category","order":"desc","orderBy":"count","include":[],"hideEmpty":true,"showNested":false,"inherit":false}} -->
	<div class="wp-block-terms-query">
		<!-- wp:term-template {"className":"sb-category-tiles__grid"} -->
			<!-- wp:group {"className":"sb-category-tile","layout":{"type":"default"}} -->
			<div class="wp-block-group sb-category-tile">
				<!-- wp:term-name {"level":3,"isLink":true,"className":"sb-category-tile__name"} /-->

				<!-- wp:term-description {"className":"sb-category-tile__desc"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:term-template -->
	</div>
	<!-- /wp:terms-query -->
</div>
<!-- /wp:group -->
