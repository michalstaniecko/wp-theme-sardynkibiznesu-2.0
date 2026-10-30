<?php
/**
 * ihumbak/toc: the Spis treści of the post from the postId context (the current post without it).
 *
 * Variant "inline" (in the content): the shared <nav><details> markup, no JS.
 * Variant "rail" (Kolumna boczna): a plain list of links plus the view script module (scroll-spy,
 * progress bar, mobile bottom bar), enqueued only when the rail renders (includes/rail.php).
 * Inside the post's own content a rail falls back to the inline markup.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ihumbak_toc_variant = in_array( $attributes['variant'] ?? '', \Ihumbak\Toc\VARIANTS, true ) ? $attributes['variant'] : 'inline';
$ihumbak_toc_post_id = (int) ( $block->context['postId'] ?? get_the_ID() );

$ihumbak_toc_in_content = doing_filter( 'the_content' ) && get_the_ID() === $ihumbak_toc_post_id;

if ( 'rail' === $ihumbak_toc_variant && ! $ihumbak_toc_in_content ) {
	echo \Ihumbak\Toc\rail_for_post( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup escaped in render_rail().
		$ihumbak_toc_post_id,
		get_block_wrapper_attributes( array( 'class' => 'ihumbak-toc--rail' ) )
	);
	return;
}

echo \Ihumbak\Toc\toc_for_post( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup escaped in render_toc().
	$ihumbak_toc_post_id,
	get_block_wrapper_attributes( array( 'class' => 'ihumbak-toc--' . ( $ihumbak_toc_in_content ? 'inline' : $ihumbak_toc_variant ) ) )
);
