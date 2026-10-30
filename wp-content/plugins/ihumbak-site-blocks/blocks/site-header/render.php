<?php
/**
 * ihumbak/site-header: frame of the header. `data-state` is `top` or `scrolled`
 * (window scroll, Interactivity API); the theme adds the shadow and stickiness.
 *
 * @var string $content Inner blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_interactivity_state(
	'ihumbak/site-header',
	array(
		'isScrolled'   => false,
		'isSearchOpen' => false,
		// Server value of the client getter `headerState` (first paint is always at the top).
		'headerState'  => 'top',
	)
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'sb-header' ) ); ?>
	data-wp-interactive="ihumbak/site-header"
	data-wp-init="callbacks.syncScrolled"
	data-wp-on-async-window--scroll="callbacks.syncScrolled"
	data-wp-bind--data-state="state.headerState"
	data-wp-class--is-search-open="state.isSearchOpen"
><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- inner blocks. ?></div>
