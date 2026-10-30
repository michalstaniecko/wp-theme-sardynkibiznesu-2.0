<?php
/**
 * Title: Sekcja: Hero
 * Slug: sardynkibiznesu-3-0/home-hero
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: hero, strona główna, wyróżniony wpis, przypięty
 * Block Types: core/query
 * Description: Sekcja strony głównej: jeden wyróżniony wpis z dużym obrazem. Pokazuje wpis przypięty, a gdy go nie ma, najnowszy.
 *
 * core/query with 1 post. Sticky posts are honoured by the query variation
 * `ihumbak/hero` (ihumbak-site-blocks): the newest sticky post, else the newest post.
 * The image is above the fold: the `render_block_core/post-featured-image` filter in theme
 * functions.php (ABOVE_THE_FOLD_IMAGES, class `sb-hero__media`) removes `loading="lazy"` and sets
 * fetchpriority="high".
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Hero"},"align":"wide","className":"sb-home-section sb-home-section--hero","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--hero">
	<!-- wp:query {"queryId":101,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"namespace":"ihumbak/hero","className":"sb-hero"} -->
	<div class="wp-block-query sb-hero">
		<!-- wp:post-template {"className":"sb-hero__list"} -->
			<!-- wp:group {"className":"sb-card sb-hero__card","layout":{"type":"default"}} -->
			<div class="wp-block-group sb-card sb-hero__card">
				<!-- wp:post-featured-image {"aspectRatio":"16/10","sizeSlug":"large","className":"sb-card__media sb-hero__media"} /-->

				<!-- wp:group {"className":"sb-card__body sb-hero__body","layout":{"type":"default"}} -->
				<div class="wp-block-group sb-card__body sb-hero__body">
					<!-- wp:group {"className":"sb-meta","layout":{"type":"default"}} -->
					<div class="wp-block-group sb-meta">
						<!-- wp:post-terms {"term":"category","separator":"","className":"sb-card__badges"} /-->

						<!-- wp:post-date {"format":"j F Y"} /-->
					</div>
					<!-- /wp:group -->

					<!-- wp:post-title {"level":2,"isLink":true,"className":"sb-card__title sb-hero__title"} /-->

					<!-- wp:post-excerpt {"excerptLength":36,"className":"sb-card__excerpt sb-hero__excerpt"} /-->

					<!-- wp:post-author-name {"className":"sb-hero__author"} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</section>
<!-- /wp:group -->
