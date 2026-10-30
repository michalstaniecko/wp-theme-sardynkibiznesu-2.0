<?php
/**
 * Title: Nagłówek archiwum
 * Slug: sardynkibiznesu-3-0/archive-header
 * Categories: text
 * Inserter: no
 * Description: Tytuł archiwum tagu i daty albo, w archiwum autora, Ramka autora z jego imieniem jako H1.
 *
 * The archive template serves tag, author and date archives. The pattern file
 * is included at render time, so it can pick the variant for the current view:
 * on author archives the only H1 is the name inside Ramka autora (.sb-author--boxed),
 * elsewhere it is core/query-title with the archive prefix ("Tag:", "Rok:").
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

if ( is_author() ) : ?>
<!-- wp:group {"className":"sb-author sb-author--boxed","layout":{"type":"default"}} -->
<div class="wp-block-group sb-author sb-author--boxed">
	<!-- wp:avatar {"size":56,"className":"sb-author__avatar"} /-->

	<!-- wp:group {"className":"sb-author__body","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-author__body">
		<!-- wp:paragraph {"className":"sb-author__role"} -->
		<p class="sb-author__role">Autor</p>
		<!-- /wp:paragraph -->

		<!-- wp:query-title {"type":"archive","level":1,"showPrefix":false,"className":"sb-author__name sb-listing__title"} /-->

		<!-- wp:post-author-biography {"className":"sb-author__bio"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
<?php else : ?>
<!-- wp:query-title {"type":"archive","level":1,"className":"sb-listing__title"} /-->

<!-- wp:term-description /-->
<?php
endif;
