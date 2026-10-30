<?php
/**
 * ihumbak/search-toggle (decision #29, pkt 4).
 *
 * Without JS: a link to the search page (`/?s=`). With JS the view module sets
 * `state.isHydrated`, which hides the link and shows a real <button> that toggles
 * the search bar (`aria-controls`, `aria-expanded`). Both are server-rendered so
 * nothing jumps; the button starts `hidden`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_interactivity_state(
	'ihumbak/site-header',
	array(
		'isHydrated'   => false,
		'isSearchOpen' => false,
	)
);

$ihumbak_label  = __( 'Szukaj', 'ihumbak-site-blocks' );
$ihumbak_search = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>';
$ihumbak_close  = '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>';
?>
<div <?php echo get_block_wrapper_attributes(); ?> data-wp-interactive="ihumbak/site-header">
	<a class="sb-icon-button sb-search-link" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php echo esc_attr( $ihumbak_label ); ?>"
		data-wp-bind--hidden="state.isHydrated"
	><?php echo $ihumbak_search; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></a>
	<button type="button" class="sb-icon-button sb-search-button" aria-controls="sb-search-bar" aria-expanded="false" aria-label="<?php echo esc_attr( $ihumbak_label ); ?>" hidden
		data-wp-init="callbacks.hydrate"
		data-wp-bind--hidden="!state.isHydrated"
		data-wp-bind--aria-expanded="state.isSearchOpen"
		data-wp-on--click="actions.toggleSearch"
	><span class="sb-search-button__icon" data-wp-bind--hidden="state.isSearchOpen"><?php echo $ihumbak_search; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="sb-search-button__icon" hidden data-wp-bind--hidden="!state.isSearchOpen"><?php echo $ihumbak_close; // phpcs:ignore WordPress.Security.EscapeOutput ?></span></button>
</div>
