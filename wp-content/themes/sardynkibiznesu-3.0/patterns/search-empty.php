<?php
/**
 * Title: Wyszukiwarka — brak wyników
 * Slug: sardynkibiznesu-3-0/search-empty
 * Categories: text
 * Inserter: no
 * Description: Widok pusty wyszukiwarki: porady i kafle kategorii. „Często szukane” i „Napisz do nas” daje część sidebar-search.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"metadata":{"name":"Porady"},"className":"sb-search-tips","layout":{"type":"default"}} -->
<div class="wp-block-group sb-search-tips">
	<!-- wp:heading {"className":"sb-search-tips__title"} -->
	<h2 class="wp-block-heading sb-search-tips__title">Spokojnie, nic nie zginęło</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>Nic nie pasuje do tego, czego szukasz. Spróbuj tak:</p>
	<!-- /wp:paragraph -->

	<!-- wp:list -->
	<ul class="wp-block-list">
		<!-- wp:list-item -->
		<li>Sprawdź literówki albo wpisz jedno słowo zamiast kilku.</li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li>Spróbuj innego słowa: „zwrot” zamiast „odstąpienie od umowy”.</li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li>Wejdź do kategorii: czasem łatwiej znaleźć, niż wyszukać.</li>
		<!-- /wp:list-item -->
	</ul>
	<!-- /wp:list -->
</div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"sardynkibiznesu-3-0/category-tiles"} /-->
