<?php
/**
 * Spis treści markup, shared by every source of the list (v1 menu, v2 shortcode, block).
 *
 * <nav aria-label="Spis treści"><details open><summary>Spis treści</summary><ul>…</ul></details></nav>
 * Collapsing is native <details>; no JS. Look and numbering belong to the theme.
 */

namespace Ihumbak\Toc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the Spis treści for a flat list of headings in document order.
 *
 * h2 are top-level items, each h3 is nested under the h2 before it (an h3 with no h2 before it
 * stays top-level). Other levels are not listed.
 *
 * @param array<int, array{level: int, id: string, text: string}> $headings
 * @return string Empty string when there is nothing to list.
 */
function render_toc( array $headings ): string {
	$tree = array();
	$last = null;

	foreach ( $headings as $heading ) {
		if ( '' === $heading['id'] ) {
			continue;
		}
		$item = array(
			'id'       => $heading['id'],
			'text'     => $heading['text'],
			'children' => array(),
		);
		if ( 2 === $heading['level'] ) {
			$tree[] = $item;
			$last   = array_key_last( $tree );
		} elseif ( 3 === $heading['level'] ) {
			if ( null === $last ) {
				$tree[] = $item;
			} else {
				$tree[ $last ]['children'][] = $item;
			}
		}
	}

	if ( ! $tree ) {
		return '';
	}

	$label = __( 'Spis treści', 'ihumbak-toc' );

	return sprintf(
		'<nav class="ihumbak-toc" aria-label="%1$s"><details class="ihumbak-toc__details" open><summary class="ihumbak-toc__summary">%2$s</summary>%3$s</details></nav>',
		esc_attr( $label ),
		esc_html( $label ),
		render_list( $tree )
	);
}

/**
 * @param array<int, array{id: string, text: string, children: array}> $items
 */
function render_list( array $items ): string {
	$html = '<ul class="ihumbak-toc__list">';
	foreach ( $items as $item ) {
		$html .= sprintf(
			'<li class="ihumbak-toc__item"><a class="ihumbak-toc__link" href="#%1$s">%2$s</a>%3$s</li>',
			esc_attr( $item['id'] ),
			esc_html( $item['text'] ),
			$item['children'] ? render_list( $item['children'] ) : ''
		);
	}
	return $html . '</ul>';
}
