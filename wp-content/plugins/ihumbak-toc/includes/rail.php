<?php
/**
 * Spis treści in the Kolumna boczna (variant "rail" of ihumbak/toc).
 *
 * Without JS it is a plain list of links. The view script module (assets/view.js, store
 * `ihumbak/toc`) adds scroll-spy (aria-current="location"), a reading progress bar and, below
 * 1024px, a bottom bar with the current heading and step ("2/6") that opens the list as a sheet.
 * The parts that need JS carry `hidden` until the store hydrates. Look belongs to the theme.
 */

namespace Ihumbak\Toc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const VIEW_MODULE = 'ihumbak-toc-view';

add_action(
	'init',
	static function (): void {
		wp_register_script_module(
			VIEW_MODULE,
			plugins_url( 'assets/view.js', __DIR__ ),
			array( '@wordpress/interactivity' ),
			(string) filemtime( dirname( __DIR__ ) . '/assets/view.js' )
		);
	}
);

/**
 * The rail for a post, with its view module enqueued; empty when the post has no h2–h3.
 *
 * @param string $wrapper_attributes Attributes for the <nav>, e.g. get_block_wrapper_attributes().
 */
function rail_for_post( int $post_id, string $wrapper_attributes = '' ): string {
	if ( ! $post_id ) {
		return '';
	}

	$html = render_rail( headings_for_post( $post_id ) );
	if ( '' === $html ) {
		return '';
	}

	wp_enqueue_script_module( VIEW_MODULE );

	return add_wrapper_attributes( $html, $wrapper_attributes );
}

/**
 * Rail markup with Interactivity directives for a flat list of headings in document order.
 *
 * @param array<int, array{level: int, id: string, text: string}> $headings
 */
function render_rail( array $headings ): string {
	$tree = build_tree( $headings );
	if ( ! $tree ) {
		return '';
	}

	// Items in link order (h3 right after its h2). The step counts top-level items, as the
	// list numbers them: an h3 shares the step ("section") of its h2.
	$items = array();
	foreach ( $tree as $index => $node ) {
		foreach ( array_merge( array( $node ), $node['children'] ) as $item ) {
			$items[] = array(
				'id'      => $item['id'],
				'text'    => $item['text'],
				'section' => $index + 1,
			);
		}
	}

	$list_id = wp_unique_id( 'ihumbak-toc-list-' );
	$label   = __( 'Spis treści', 'ihumbak-toc' );
	$context = array(
		'items'    => $items,
		'sections' => count( $tree ),
		'active'   => -1,
		'step'     => sprintf( '1/%d', count( $tree ) ),
		'current'  => $items[0]['text'],
		'progress' => '0%',
		'open'     => false,
		'covering' => false,
	);

	$chevron = '<svg class="ihumbak-toc__chevron" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M18 15l-6-6-6 6"/></svg>';

	return sprintf(
		'<nav class="ihumbak-toc" aria-label="%1$s" data-wp-interactive="ihumbak/toc" data-wp-context="%2$s"'
			. ' data-wp-init="callbacks.init" data-wp-on-async-window--scroll="callbacks.spy" data-wp-on-async-window--resize="callbacks.spy"'
			. ' data-wp-on--keydown="actions.closeOnEscape" data-wp-on-document--click="actions.closeOnOutside" data-wp-class--is-enhanced="state.enhanced" data-wp-class--is-open="context.open" data-wp-class--is-covering="context.covering">'
			. '<p class="ihumbak-toc__heading">%3$s</p>'
			. '<div class="ihumbak-toc__progress" hidden data-wp-bind--hidden="!state.enhanced"><span class="ihumbak-toc__progress-bar" data-wp-style--width="context.progress"></span></div>'
			. '<button class="ihumbak-toc__toggle" type="button" aria-controls="%4$s" aria-expanded="false" hidden data-wp-bind--hidden="!state.enhanced" data-wp-bind--aria-expanded="context.open" data-wp-on--click="actions.toggle">'
			. '<span class="ihumbak-toc__sr">%5$s: </span><span class="ihumbak-toc__step" data-wp-text="context.step">%6$s</span> <span class="ihumbak-toc__current" data-wp-text="context.current">%7$s</span>%8$s</button>'
			. '%9$s</nav>',
		esc_attr( $label ),
		esc_attr( (string) wp_json_encode( $context, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) ),
		esc_html__( 'W tym wpisie', 'ihumbak-toc' ),
		esc_attr( $list_id ),
		esc_html( $label ),
		esc_html( $context['step'] ),
		esc_html( $context['current'] ),
		$chevron,
		render_list(
			$tree,
			sprintf( 'id="%s" data-wp-on--click="actions.closeOnLink"', esc_attr( $list_id ) ),
			'data-wp-bind--aria-current="state.ariaCurrent"'
		)
	);
}
