<?php
/**
 * Title: Sekcja: Podcast
 * Slug: sardynkibiznesu-3-0/home-podcast
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: podcast, odcinek, spotify, apple podcasts, youtube
 * Description: Sekcja strony głównej: najnowszy odcinek z kategorii Podcasty i trzy kolejne, z numerem odcinka, czasem trwania i linkami do platform.
 *
 * Two core/query blocks on the Podcasty category (1 post; 3 posts with offset 1). Episode
 * number and duration are post meta (`sb_podcast_episode`, `sb_podcast_duration`,
 * ihumbak-content-elements) shown through Block Bindings `core/post-meta`; an episode
 * without the meta shows no badge or duration (empty paragraphs are hidden by CSS).
 * Platforms are core/buttons.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_podcast_term = get_term_by( 'slug', 'podcasty', 'category' );
$sb_podcast_id   = $sb_podcast_term ? (int) $sb_podcast_term->term_id : 0;
$sb_podcast_url  = $sb_podcast_term ? get_term_link( $sb_podcast_term ) : home_url( '/' );
if ( is_wp_error( $sb_podcast_url ) ) {
	$sb_podcast_url = home_url( '/' );
}
$sb_query = static fn ( int $id, int $per_page, int $offset ): string => wp_json_encode(
	array(
		'perPage'  => $per_page,
		'pages'    => 0,
		'offset'   => $offset,
		'postType' => 'post',
		'order'    => 'desc',
		'orderBy'  => 'date',
		'author'   => '',
		'search'   => '',
		'exclude'  => array(),
		'sticky'   => 'ignore',
		'inherit'  => false,
		'taxQuery' => array( 'category' => array( $id ) ),
	)
);
$sb_episode  = '{"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"sb_podcast_episode"}}}},"className":"is-style-badge sb-podcast__episode"}';
$sb_duration = '{"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"sb_podcast_duration"}}}},"className":"sb-podcast__duration"}';
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Podcast"},"align":"wide","className":"sb-home-section sb-home-section--podcast","layout":{"type":"default"}} -->
<section class="wp-block-group alignwide sb-home-section sb-home-section--podcast">
	<!-- wp:group {"className":"sb-home-section__header","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-home-section__header">
		<!-- wp:heading {"className":"sb-home-section__title"} -->
		<h2 class="wp-block-heading sb-home-section__title">Podcast Sardynki Biznesu</h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"sb-home-section__more"} -->
		<p class="sb-home-section__more"><a href="<?php echo esc_url( $sb_podcast_url ); ?>">Wszystkie odcinki</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"sb-podcast","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-podcast">
		<!-- wp:query {"queryId":103,"query":<?php echo $sb_query( $sb_podcast_id, 1, 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON of ints and literals. ?>,"className":"sb-podcast-featured"} -->
		<div class="wp-block-query sb-podcast-featured">
			<!-- wp:post-template -->
				<!-- wp:group {"className":"sb-card sb-podcast__card sb-podcast__card--featured","layout":{"type":"default"}} -->
				<div class="wp-block-group sb-card sb-podcast__card sb-podcast__card--featured">
					<!-- wp:post-featured-image {"aspectRatio":"16/9","sizeSlug":"medium_large","className":"sb-card__media sb-podcast__media"} /-->

					<!-- wp:group {"className":"sb-card__body","layout":{"type":"default"}} -->
					<div class="wp-block-group sb-card__body">
						<!-- wp:group {"className":"sb-podcast__meta","layout":{"type":"default"}} -->
						<div class="wp-block-group sb-podcast__meta">
							<!-- wp:paragraph <?php echo $sb_episode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal. ?> -->
							<p class="is-style-badge sb-podcast__episode"></p>
							<!-- /wp:paragraph -->

							<!-- wp:paragraph <?php echo $sb_duration; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal. ?> -->
							<p class="sb-podcast__duration"></p>
							<!-- /wp:paragraph -->
						</div>
						<!-- /wp:group -->

						<!-- wp:post-title {"level":3,"isLink":true,"className":"sb-card__title"} /-->

						<!-- wp:post-excerpt {"excerptLength":30,"className":"sb-card__excerpt"} /-->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
			<!-- /wp:post-template -->
		</div>
		<!-- /wp:query -->

		<!-- wp:group {"className":"sb-podcast__aside","layout":{"type":"default"}} -->
		<div class="wp-block-group sb-podcast__aside">
			<!-- wp:query {"queryId":104,"query":<?php echo $sb_query( $sb_podcast_id, 3, 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON of ints and literals. ?>,"className":"sb-podcast-list"} -->
			<div class="wp-block-query sb-podcast-list">
				<!-- wp:post-template {"className":"sb-podcast__list"} -->
					<!-- wp:group {"className":"sb-card sb-podcast__card","layout":{"type":"default"}} -->
					<div class="wp-block-group sb-card sb-podcast__card">
						<!-- wp:group {"className":"sb-podcast__meta","layout":{"type":"default"}} -->
						<div class="wp-block-group sb-podcast__meta">
							<!-- wp:paragraph <?php echo $sb_episode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal. ?> -->
							<p class="is-style-badge sb-podcast__episode"></p>
							<!-- /wp:paragraph -->

							<!-- wp:paragraph <?php echo $sb_duration; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- literal. ?> -->
							<p class="sb-podcast__duration"></p>
							<!-- /wp:paragraph -->
						</div>
						<!-- /wp:group -->

						<!-- wp:post-title {"level":3,"isLink":true,"className":"sb-card__title"} /-->
					</div>
					<!-- /wp:group -->
				<!-- /wp:post-template -->
			</div>
			<!-- /wp:query -->

			<!-- wp:group {"className":"sb-podcast__platforms","layout":{"type":"default"}} -->
			<div class="wp-block-group sb-podcast__platforms">
				<!-- wp:paragraph {"className":"sb-podcast__platforms-label"} -->
				<p class="sb-podcast__platforms-label">Słuchaj na</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://open.spotify.com/show/17a8wLavmR28DH43b1RLr6">Spotify</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://podcasts.apple.com/pl/podcast/sardynkibiznesu-pl/id1446983303">Apple Podcasts</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="https://www.youtube.com/channel/UC5W8ggvTgpY3q9g3msgC2Gg">YouTube</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
