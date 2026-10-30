<?php
/**
 * ihumbak/toc: the Spis treści of the post from the postId context (the current post without it).
 *
 * Variant "inline" (in the content): the shared <nav><details> markup, no JS.
 * Variant "rail" (Kolumna boczna) gets the class ihumbak-toc--rail; its script comes with #49.
 * Outside the content, "inline" prints nothing when the content shows its own Spis treści.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ihumbak_toc_variant = in_array( $attributes['variant'] ?? '', \Ihumbak\Toc\VARIANTS, true ) ? $attributes['variant'] : 'inline';
$ihumbak_toc_post_id = (int) ( $block->context['postId'] ?? get_the_ID() );

echo \Ihumbak\Toc\toc_for_post( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup escaped in render_toc().
	$ihumbak_toc_post_id,
	get_block_wrapper_attributes( array( 'class' => 'ihumbak-toc--' . $ihumbak_toc_variant ) ),
	// An inline list placed in a template (e.g. page-narrow) gives way to one in the content.
	'inline' === $ihumbak_toc_variant
);
