<?php
/**
 * ihumbak/most-read: numbered list of the most read posts (`.sb-ranking`).
 * Renders nothing (no wrapper) when there are no posts. View counts are never shown.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use Ihumbak\SiteBlocks\MostRead;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ihumbak_category = MostRead\scoped_category( (string) ( $attributes['scope'] ?? 'auto' ) );
$ihumbak_posts    = MostRead\posts( (int) ( $attributes['count'] ?? MostRead\DEFAULT_COUNT ), $ihumbak_category );

// The daily sync purges this LiteSpeed tag, so cached pages pick up the new ranking.
do_action( 'litespeed_tag_add', Ihumbak\SiteBlocks\Popularity\CACHE_TAG );

if ( ! $ihumbak_posts ) {
	return;
}

$ihumbak_heading_id = wp_unique_id( 'sb-ranking-' );
$ihumbak_heading    = $ihumbak_category
	? __( 'Najczęściej czytane w tej kategorii', 'ihumbak-site-blocks' )
	: __( 'Najczęściej czytane', 'ihumbak-site-blocks' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'sb-ranking', 'aria-labelledby' => $ihumbak_heading_id ) ); ?>>
	<h2 class="sb-ranking__heading" id="<?php echo esc_attr( $ihumbak_heading_id ); ?>"><?php echo esc_html( $ihumbak_heading ); ?></h2>
	<ol class="sb-ranking__list" role="list">
		<?php foreach ( $ihumbak_posts as $ihumbak_post ) : ?>
			<?php $ihumbak_terms = $ihumbak_category ? array() : get_the_category( $ihumbak_post->ID ); ?>
			<li class="sb-ranking__item">
				<a class="sb-ranking__link" href="<?php echo esc_url( get_permalink( $ihumbak_post ) ); ?>"><?php echo esc_html( get_the_title( $ihumbak_post ) ); ?></a>
				<?php if ( $ihumbak_terms ) : ?>
					<span class="sb-ranking__meta"><?php echo esc_html( $ihumbak_terms[0]->name ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
