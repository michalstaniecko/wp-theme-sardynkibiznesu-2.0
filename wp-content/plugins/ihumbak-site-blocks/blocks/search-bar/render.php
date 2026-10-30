<?php
/**
 * ihumbak/search-bar: core/search + popular terms, below the header.
 *
 * Fixed `id="sb-search-bar"` for the toggle's aria-controls (`multiple: false`).
 * Hidden until opened; on open the search field gets focus, Escape closes the bar
 * and returns focus to the toggle (view module, store `ihumbak/site-header`).
 *
 * @var string $content Inner blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_interactivity_state( 'ihumbak/site-header', array( 'isSearchOpen' => false ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'id' => 'sb-search-bar' ) ); ?> hidden
	data-wp-interactive="ihumbak/site-header"
	data-wp-bind--hidden="!state.isSearchOpen"
	data-wp-watch="callbacks.focusSearch"
	data-wp-on-document--keydown="actions.closeOnEscape"
><div class="wp-block-ihumbak-search-bar__inner"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- inner blocks. ?></div></div>
