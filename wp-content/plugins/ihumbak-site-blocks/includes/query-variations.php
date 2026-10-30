<?php
/**
 * Server side of `core/query` variations, shared by „Z tej kategorii” (same-category-query.php)
 * and the Hero / Najnowsze sections (home-queries.php).
 *
 * A variation is recognised by the block's `namespace` attribute. While that block renders
 * (from `pre_render_block` to `render_block_core/query`), its post template gets the
 * variation's extra query vars through `query_loop_block_query_vars`; afterwards the filter
 * is removed, so other query blocks on the page are untouched. An optional `$after_render`
 * callback may change the block's HTML (e.g. drop it when it has no posts).
 */

namespace Ihumbak\SiteBlocks\QueryVariations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registered variations by name.
 *
 * @return array<string, array{query_vars: callable, after_render: ?callable}>
 */
function &registry(): array {
	static $variations = array();
	return $variations;
}

/**
 * Registers the server side of a query variation.
 *
 * @param string        $name         The variation's `namespace` attribute, e.g. `ihumbak/hero`.
 * @param callable      $query_vars   `query_loop_block_query_vars` filter: (array $query): array.
 * @param callable|null $after_render (string $html): string, applied to the rendered block.
 */
function register( string $name, callable $query_vars, ?callable $after_render = null ): void {
	$variations          = &registry();
	$variations[ $name ] = array(
		'query_vars'   => $query_vars,
		'after_render' => $after_render,
	);
}

/** @return array{query_vars: callable, after_render: ?callable}|null The variation of the block, if it is one. */
function variation_of( array $parsed_block ): ?array {
	if ( 'core/query' !== ( $parsed_block['blockName'] ?? '' ) ) {
		return null;
	}
	return registry()[ $parsed_block['attrs']['namespace'] ?? '' ] ?? null;
}

add_filter(
	'pre_render_block',
	static function ( $pre_render, array $parsed_block ) {
		$variation = variation_of( $parsed_block );
		if ( $variation ) {
			add_filter( 'query_loop_block_query_vars', $variation['query_vars'] );
		}
		return $pre_render;
	},
	10,
	2
);

add_filter(
	'render_block_core/query',
	static function ( string $html, array $parsed_block ): string {
		$variation = variation_of( $parsed_block );
		if ( ! $variation ) {
			return $html;
		}
		remove_filter( 'query_loop_block_query_vars', $variation['query_vars'] );
		return $variation['after_render'] ? ( $variation['after_render'] )( $html ) : $html;
	},
	10,
	2
);
