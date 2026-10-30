<?php
/**
 * „Liczba wpisów” of a term (spec #36: the term block missing from core for the
 * Sekcja strony głównej „O czym piszemy”, #58). core/term-count prints only the number;
 * the tiles need it with the noun in the right Polish form. Markup: blocks/term-post-count.
 */

namespace Ihumbak\SiteBlocks\TermPostCount;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** "1 wpis", "2 wpisy", "5 wpisów", "12 wpisów", "22 wpisy", "113 wpisów". */
function label( int $count ): string {
	$tens = $count % 100;
	$ones = $count % 10;
	if ( 1 === $count ) {
		$noun = 'wpis';
	} elseif ( $ones >= 2 && $ones <= 4 && ( $tens < 12 || $tens > 14 ) ) {
		$noun = 'wpisy';
	} else {
		$noun = 'wpisów';
	}
	return number_format_i18n( $count ) . ' ' . $noun;
}
