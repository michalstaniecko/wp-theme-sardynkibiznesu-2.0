<?php
/**
 *
 * Plugin Name: iHumbak Compare Table
 * Description: Feature comparison table for services (plans, tools, providers) built from nested Gutenberg blocks and rendered in PHP.
 * Version: 1.0.0
 * Author: iHumbak
 * Text Domain: ihumbak-compare-table
 * Requires at least: 6.6
 * Requires PHP: 7.4
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IH_COMPARE_TABLE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IH_COMPARE_TABLE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( file_exists( IH_COMPARE_TABLE_PLUGIN_DIR . 'inc/Renderer.php' ) ) {
	require_once IH_COMPARE_TABLE_PLUGIN_DIR . 'inc/Renderer.php';
}

add_action( 'init', 'ihumbak_compare_table_load_textdomain' );
add_action( 'init', 'ihumbak_compare_table_register_assets', 9 );
add_action( 'init', 'ihumbak_compare_table_register_blocks' );
add_action( 'wp_enqueue_scripts', 'ihumbak_compare_table_enqueue_frontend' );

function ihumbak_compare_table_load_textdomain() {
	load_plugin_textdomain(
		'ihumbak-compare-table',
		false,
		basename( dirname( __FILE__ ) ) . '/languages/'
	);
}

/**
 * Version string for a built file, so browsers refetch it after a rebuild.
 */
function ihumbak_compare_table_asset_version( $relative_path ) {
	$path = IH_COMPARE_TABLE_PLUGIN_DIR . $relative_path;

	return file_exists( $path ) ? (string) filemtime( $path ) : '1.0.0';
}

/**
 * Registers (does not enqueue) the handles shared by the editor and the frontend:
 *
 * - style  `ihumbak-compare-table`         build/style.css   frontend structural CSS
 * - style  `ihumbak-compare-table-editor`  build/editor.css  editor-only overrides, depends on the one above
 * - script `ihumbak-compare-table-view`    build/view.js     viewScript of ihumbak/compare-table
 */
function ihumbak_compare_table_register_assets() {
	$has_style = file_exists( IH_COMPARE_TABLE_PLUGIN_DIR . 'build/style.css' );

	if ( $has_style ) {
		wp_register_style(
			'ihumbak-compare-table',
			IH_COMPARE_TABLE_PLUGIN_URL . 'build/style.css',
			array(),
			ihumbak_compare_table_asset_version( 'build/style.css' )
		);
	}

	if ( file_exists( IH_COMPARE_TABLE_PLUGIN_DIR . 'build/editor.css' ) ) {
		wp_register_style(
			'ihumbak-compare-table-editor',
			IH_COMPARE_TABLE_PLUGIN_URL . 'build/editor.css',
			$has_style ? array( 'ihumbak-compare-table' ) : array(),
			ihumbak_compare_table_asset_version( 'build/editor.css' )
		);
	}

	if ( file_exists( IH_COMPARE_TABLE_PLUGIN_DIR . 'build/view.js' ) ) {
		$asset_file = IH_COMPARE_TABLE_PLUGIN_DIR . 'build/view.asset.php';
		$asset      = file_exists( $asset_file ) ? require $asset_file : array();

		wp_register_script(
			'ihumbak-compare-table-view',
			IH_COMPARE_TABLE_PLUGIN_URL . 'build/view.js',
			isset( $asset['dependencies'] ) ? $asset['dependencies'] : array(),
			isset( $asset['version'] ) ? $asset['version'] : ihumbak_compare_table_asset_version( 'build/view.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}

/**
 * Registers the block tree from the built metadata.
 *
 * Only ihumbak/compare-table produces markup: its callback walks the parsed
 * block tree itself, so the children are registered (the editor and the block
 * parser need to know them) but render nothing on their own.
 */
function ihumbak_compare_table_register_blocks() {
	$blocks = array(
		'compare-table',
		'compare-header',
		'compare-column',
		'compare-section',
		'compare-row',
		'compare-value',
	);

	$has_renderer = class_exists( '\Ihumbak\CompareTable\Renderer' );

	foreach ( $blocks as $block ) {
		$dir = IH_COMPARE_TABLE_PLUGIN_DIR . 'build/blocks/' . $block;

		if ( ! file_exists( $dir . '/block.json' ) ) {
			continue;
		}

		$args = array( 'render_callback' => '__return_empty_string' );

		if ( 'compare-table' === $block && $has_renderer ) {
			$args = array(
				'render_callback'   => array( \Ihumbak\CompareTable\Renderer::class, 'render' ),
				// The renderer reads $block->parsed_block, so rendering 200+ inner blocks to '' is wasted work.
				'skip_inner_blocks' => true,
			);
		}

		register_block_type( $dir, $args );
	}
}

/**
 * Loads the stylesheet in the head on single views that use the block. On an
 * archive has_block() would only look at the first post of the loop, so every
 * other case (archives, widgets, reusable blocks) is left to the render
 * callback, which enqueues the same handle.
 */
function ihumbak_compare_table_enqueue_frontend() {
	if ( is_singular() && has_block( 'ihumbak/compare-table' ) ) {
		wp_enqueue_style( 'ihumbak-compare-table' );
	}
}
