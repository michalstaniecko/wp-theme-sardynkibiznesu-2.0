<?php
/**
 * Title: Strona główna
 * Slug: sardynkibiznesu-3-0/home
 * Categories: sardynkibiznesu-3-0-home
 * Keywords: strona główna, sekcje, home
 * Block Types: core/post-content
 * Post Types: page
 * Description: Treść strony głównej: osiem Sekcji strony głównej w domyślnej kolejności (Hero, Najnowsze, Polecamy na start, Podcast, Newsletter, Najczęściej czytane, O czym piszemy, Darmowe narzędzia).
 *
 * Offered when a new page is created. The front page is an ordinary page (show_on_front=page,
 * template front-page): its content is these sections, so editors reorder and edit them
 * like any blocks, with revisions. Each section is also its own pattern in the
 * „Sekcje strony głównej” category, so a removed section can be inserted again.
 * The sections are inlined (not wp:pattern references), so the saved page holds plain blocks.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_registry = WP_Block_Patterns_Registry::get_instance();
foreach ( \SardynkiBiznesu\Theme\HOME_SECTIONS as $sb_section ) {
	$sb_pattern = $sb_registry->get_registered( 'sardynkibiznesu-3-0/home-' . $sb_section );
	if ( $sb_pattern ) {
		echo $sb_pattern['content'] . "\n\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block markup of the theme's own patterns.
	}
}
