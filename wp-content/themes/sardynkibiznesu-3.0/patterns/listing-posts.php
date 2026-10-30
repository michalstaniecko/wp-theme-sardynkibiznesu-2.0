<?php
/**
 * Title: Lista wpisów (wiersze)
 * Slug: sardynkibiznesu-3-0/listing-posts
 * Categories: posts
 * Inserter: no
 * Description: Główna pętla list (/blog/, kategoria, archiwa): karty .sb-card--row, paginacja i widok pusty.
 *
 * Shared by templates home, category and archive. The query inherits the main
 * query, so pagination, ordering (and later popularity sorting, #55) come from
 * the URL. Card titles are H2: the template's only H1 is the view title.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"className":"sb-listing__query"} -->
<div class="wp-block-query sb-listing__query">
	<!-- wp:post-template {"className":"sb-list"} -->
		<!-- wp:group {"className":"sb-card sb-card--row","layout":{"type":"default"}} -->
		<div class="wp-block-group sb-card sb-card--row">
			<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/10","sizeSlug":"medium_large","className":"sb-card__media sb-card__media--row"} /-->

			<!-- wp:group {"className":"sb-card__body","layout":{"type":"default"}} -->
			<div class="wp-block-group sb-card__body">
				<!-- wp:group {"className":"sb-meta","layout":{"type":"default"}} -->
				<div class="wp-block-group sb-meta">
					<!-- wp:post-terms {"term":"category","separator":"","className":"sb-card__badges"} /-->

					<!-- wp:post-date {"format":"j F Y"} /-->
				</div>
				<!-- /wp:group -->

				<!-- wp:post-title {"level":2,"isLink":true,"className":"sb-card__title"} /-->

				<!-- wp:post-excerpt {"excerptLength":30,"className":"sb-card__excerpt"} /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"paginationArrow":"chevron","className":"sb-pagination","layout":{"type":"flex","justifyContent":"left","flexWrap":"wrap"}} -->
		<!-- wp:query-pagination-previous {"label":"Poprzednia"} /-->

		<!-- wp:query-pagination-numbers {"midSize":1} /-->

		<!-- wp:query-pagination-next {"label":"Następna"} /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:paragraph -->
		<p>Nie ma tu jeszcze żadnych wpisów.</p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
