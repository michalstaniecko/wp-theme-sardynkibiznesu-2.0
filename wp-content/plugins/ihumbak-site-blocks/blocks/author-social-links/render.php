<?php
/**
 * ihumbak/author-social-links: links of the post author in Ramka autora.
 * Renders nothing (no wrapper) when the author filled in none.
 *
 * @var WP_Block $block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ihumbak_post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
$ihumbak_author  = $ihumbak_post_id ? (int) get_post_field( 'post_author', $ihumbak_post_id ) : 0;
$ihumbak_links   = $ihumbak_author ? ihumbak_site_blocks_author_links( $ihumbak_author ) : array();
if ( ! $ihumbak_links ) {
	return;
}

$ihumbak_networks = ihumbak_site_blocks_author_networks();
$ihumbak_name     = get_the_author_meta( 'display_name', $ihumbak_author );
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<ul class="wp-block-ihumbak-author-social-links__list">
		<?php foreach ( $ihumbak_links as $ihumbak_key => $ihumbak_url ) : ?>
			<li><a class="wp-block-ihumbak-author-social-links__link" href="<?php echo esc_url( $ihumbak_url ); ?>" rel="me nofollow noopener" aria-label="<?php echo esc_attr( sprintf( '%1$s: %2$s', $ihumbak_networks[ $ihumbak_key ]['label'], $ihumbak_name ) ); ?>"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false"><?php echo $ihumbak_networks[ $ihumbak_key ]['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></svg></a></li>
		<?php endforeach; ?>
	</ul>
</div>
