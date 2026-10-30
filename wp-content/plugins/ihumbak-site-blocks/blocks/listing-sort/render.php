<?php
/**
 * ihumbak/listing-sort: sort links of a category archive. Plain links (works without JS);
 * the active one has aria-current="page". Renders nothing outside category archives.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use Ihumbak\SiteBlocks\ListingSort;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_category() ) {
	return;
}

$ihumbak_current = ListingSort\current();
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<nav class="wp-block-ihumbak-listing-sort__nav" aria-label="<?php echo esc_attr__( 'Sortowanie wpisów', 'ihumbak-site-blocks' ); ?>">
		<span class="wp-block-ihumbak-listing-sort__label" aria-hidden="true"><?php esc_html_e( 'Sortuj:', 'ihumbak-site-blocks' ); ?></span>
		<ul class="wp-block-ihumbak-listing-sort__list" role="list">
			<?php foreach ( ListingSort\options() as $ihumbak_value => $ihumbak_label ) : ?>
				<li><a class="wp-block-ihumbak-listing-sort__link" href="<?php echo esc_url( ListingSort\url( $ihumbak_value ) ); ?>"<?php echo $ihumbak_value === $ihumbak_current ? ' aria-current="page"' : ''; ?><?php echo ListingSort\NEWEST === $ihumbak_value ? '' : ' rel="nofollow"'; ?>><?php echo esc_html( $ihumbak_label ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>
