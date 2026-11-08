<?php
/**
 * Kalkulator VAT assets: one stylesheet and two script modules (no jQuery, no build step).
 *
 * - ihumbak-tax-calculator-calc: assets/calc.js, the pure calculation module.
 * - ihumbak-tax-calculator-view: assets/view.js, the form handling; imports the calc module
 *   by its import-map id, so both get ?ver= and modulepreload from core.
 *
 * Both modules print in the footer (type="module", never render-blocking).
 */

namespace Ihumbak\TaxCalculator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const STYLE       = 'ihumbak-tax-calculator';
const CALC_MODULE = 'ihumbak-tax-calculator-calc';
const VIEW_MODULE = 'ihumbak-tax-calculator-view';

/**
 * Registers the assets and, on a singular page whose content has the shortcode, enqueues
 * them right away.
 *
 * Why two places enqueue: on a classic theme (2.0) the shortcode renders after wp_head,
 * so only the early enqueue here puts the stylesheet in <head>. A block theme (3.0) renders
 * the template, and so the shortcode, before wp_head and before this hook: enqueue_assets()
 * from the render then runs first, and core keeps such handles until they are registered
 * here (WP_Dependencies::$queued_before_register, WP_Script_Modules queue). The shortcode
 * outside post content (e.g. in a widget) is covered by the render only.
 */
add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$dir = plugin_dir_path( IHUMBAK_TAX_CALCULATOR_FILE );

		wp_register_style(
			STYLE,
			plugins_url( 'assets/tax-calculator.css', IHUMBAK_TAX_CALCULATOR_FILE ),
			array(),
			IHUMBAK_TAX_CALCULATOR_VERSION
		);
		wp_style_add_data( STYLE, 'path', $dir . 'assets/tax-calculator.css' );

		wp_register_script_module(
			CALC_MODULE,
			plugins_url( 'assets/calc.js', IHUMBAK_TAX_CALCULATOR_FILE ),
			array(),
			IHUMBAK_TAX_CALCULATOR_VERSION,
			array( 'in_footer' => true )
		);

		$view = require $dir . 'assets/view.asset.php';
		wp_register_script_module(
			VIEW_MODULE,
			plugins_url( 'assets/view.js', IHUMBAK_TAX_CALCULATOR_FILE ),
			$view['dependencies'],
			$view['version'],
			array( 'in_footer' => true )
		);

		if ( is_singular() && has_shortcode( (string) get_post_field( 'post_content', get_queried_object_id() ), SHORTCODE ) ) {
			enqueue_assets();
		}
	}
);

/** Queues the stylesheet and the view module (the calc module comes in as its dependency). */
function enqueue_assets(): void {
	wp_enqueue_style( STYLE );
	wp_enqueue_script_module( VIEW_MODULE );
}
