<?php
/**
 * Title: Sekcja: Najnowsze
 * Slug: sardynkibiznesu-3-0/home-latest
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: najnowsze, wpisy, strona główna, blog
 * Block Types: core/query
 * Description: Sekcja strony głównej: trzy najnowsze wpisy po wpisie z Hero, z linkiem do /blog/.
 *
 * core/query with 3 posts and offset 1. The query variation `ihumbak/latest`
 * (ihumbak-site-blocks) skips the hero's post instead of the first one when the hero shows
 * a sticky post. Card titles are H3 under the section's H2.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_blog_id  = (int) get_option( 'page_for_posts' );
$sb_blog_url = $sb_blog_id ? get_permalink( $sb_blog_id ) : home_url( '/blog/' );
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Najnowsze"},"align":"wide","className":"sb-home-section sb-home-section--latest","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--latest">
	<!-- wp:group {"className":"sb-home-section__header","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-home-section__header">
		<!-- wp:heading {"className":"sb-home-section__title"} -->
		<h2 class="wp-block-heading sb-home-section__title">Najnowsze wpisy</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"sb-home-section__more"} -->
		<p class="sb-home-section__more"><a href="<?php echo esc_url( $sb_blog_url ); ?>">Wszystkie wpisy</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":102,"query":{"perPage":3,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"namespace":"ihumbak/latest","className":"sb-home-latest"} -->
	<div class="wp-block-query sb-home-latest">
		<!-- wp:post-template {"className":"sb-grid"} -->
			<!-- wp:group {"className":"sb-card sb-card--col","layout":{"type":"default"}} -->
			<div class="wp-block-group sb-card sb-card--col">
				<!-- wp:post-featured-image {"aspectRatio":"16/10","sizeSlug":"medium_large","className":"sb-card__media sb-card__media--col"} /-->

				<!-- wp:group {"className":"sb-card__body","layout":{"type":"default"}} -->
				<div class="wp-block-group sb-card__body">
					<!-- wp:group {"className":"sb-meta","layout":{"type":"default"}} -->
					<div class="wp-block-group sb-meta">
						<!-- wp:post-terms {"term":"category","separator":"","className":"sb-card__badges"} /-->

						<!-- wp:post-date {"format":"j F Y"} /-->
					</div>
					<!-- /wp:group -->

					<!-- wp:post-title {"level":3,"isLink":true,"className":"sb-card__title"} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</section>
<!-- /wp:group -->
