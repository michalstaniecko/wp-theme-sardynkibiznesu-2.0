<?php
/**
 * ihumbak/term-post-count: "113 wpisów" for the term in context (core/term-template).
 * Renders nothing without a term.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use Ihumbak\SiteBlocks\TermPostCount;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ihumbak_term = isset( $block->context['termId'], $block->context['taxonomy'] )
	? get_term( (int) $block->context['termId'], (string) $block->context['taxonomy'] )
	: null;

if ( ! $ihumbak_term instanceof WP_Term ) {
	return;
}
?>
<p <?php echo get_block_wrapper_attributes(); ?>><?php echo esc_html( TermPostCount\label( (int) $ihumbak_term->count ) ); ?></p>
