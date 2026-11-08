<?php
/**
 * Plugin Name:       iHumbak Tax Calculator
 * Description:       Kalkulator VAT (netto ↔ brutto, stawki 23 %, 8 %, 5 %) jako shortcode [ihumbak_tax_calculator]. Liczy w przeglądarce (moduł ES bez zależności); bez JavaScriptu pokazuje komunikat.
 * Version:           1.0.0
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            iHumbak
 * Text Domain:       ihumbak-tax-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_TAX_CALCULATOR_FILE    = __FILE__;
const IHUMBAK_TAX_CALCULATOR_VERSION = '1.0.0';

require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/calculator.php';
