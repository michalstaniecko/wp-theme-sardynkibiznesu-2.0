<?php
/**
 * Title: Zacznij od tych wpisów
 * Slug: sardynkibiznesu-3-0/start-posts
 * Categories: text
 * Keywords: zacznij, polecane wpisy, kolumna boczna
 * Description: Trzy wybrane wpisy na start (kategoria, czas czytania, tytuł z linkiem). Używane w Kolumnie bocznej stron.
 *
 * Starting content only: the list is static block markup, so editors swap posts in the Site
 * Editor (part sidebar-page). Until the part is edited, titles, links, categories and reading
 * times are read from the posts below, so they follow renames and permalink changes.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_start_slugs = array(
	'super-tajna-liczba-bez-ktorej-nie-zrobisz-skutecznego-marketingu-w-malej-firmie',
	'czy-rodo-i-polityka-prywatnosci-to-to-samo',
	'rodo-firma-przedsiebiorca',
);

$sb_start_cards = array();
foreach ( $sb_start_slugs as $sb_start_slug ) {
	$sb_start_post = get_page_by_path( $sb_start_slug, OBJECT, 'post' );
	if ( ! $sb_start_post || 'publish' !== $sb_start_post->post_status ) {
		continue;
	}
	$sb_start_terms = get_the_category( $sb_start_post->ID );
	// Same rate as core/post-time-to-read: 189 words per minute, at least 1.
	$sb_start_words   = count( preg_split( '/\s+/u', wp_strip_all_tags( strip_shortcodes( $sb_start_post->post_content ) ), -1, PREG_SPLIT_NO_EMPTY ) );
	$sb_start_cards[] = array(
		'url'      => get_permalink( $sb_start_post ),
		'title'    => get_the_title( $sb_start_post ),
		'category' => $sb_start_terms ? $sb_start_terms[0]->name : '',
		'minutes'  => max( 1, (int) round( $sb_start_words / 189 ) ),
	);
}

if ( ! $sb_start_cards ) {
	return;
}
?>
<!-- wp:group {"metadata":{"name":"Zacznij od tych wpisów"},"className":"sb-start-posts","layout":{"type":"default"}} -->
<div class="wp-block-group sb-start-posts">
	<!-- wp:heading {"className":"sb-sidebar__heading"} -->
	<h2 class="wp-block-heading sb-sidebar__heading">Zacznij od tych wpisów</h2>
	<!-- /wp:heading -->
<?php foreach ( $sb_start_cards as $sb_start_card ) : ?>

	<!-- wp:group {"className":"sb-card sb-card--mini","layout":{"type":"default"}} -->
	<div class="wp-block-group sb-card sb-card--mini">
		<!-- wp:group {"className":"sb-meta","layout":{"type":"default"}} -->
		<div class="wp-block-group sb-meta">
			<?php if ( '' !== $sb_start_card['category'] ) : ?>
			<!-- wp:paragraph {"className":"is-style-badge"} -->
			<p class="is-style-badge"><?php echo esc_html( $sb_start_card['category'] ); ?></p>
			<!-- /wp:paragraph -->

			<?php endif; ?>
			<!-- wp:paragraph -->
			<p><?php echo esc_html( $sb_start_card['minutes'] . ' min' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:heading {"level":3,"className":"sb-card__title"} -->
		<h3 class="wp-block-heading sb-card__title"><a href="<?php echo esc_url( $sb_start_card['url'] ); ?>"><?php echo esc_html( $sb_start_card['title'] ); ?></a></h3>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
