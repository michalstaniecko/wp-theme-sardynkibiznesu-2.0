<?php
/**
 * 2.0 page templates in 3.0 (#20, #57): pages saved with a 2.0 PHP page template keep their
 * `_wp_page_template` meta (2.0 keeps working on the same database) and render with the
 * matching 3.0 block template. Content compatibility, so it lives here and not in the
 * theme (ADR-0001). A theme without the target template falls back to `page`.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\LegacyPageTemplates;

defined( 'ABSPATH' ) || exit;

/**
 * 2.0 page template meta → 3.0 block template. The 18 pages saved with the 2.0 template
 * „Boxed without sidebar” render with `page-narrow`.
 */
const LEGACY_PAGE_TEMPLATES = array(
	'templates/boxed-without-sidebar.php' => 'page-narrow.php',
);

add_filter(
	'page_template_hierarchy',
	static function ( array $templates ): array {
		$mapped = array();
		foreach ( $templates as $template ) {
			// The 3.0 template first, the 2.0 file right after it: 2.0 still finds its own template.
			if ( isset( LEGACY_PAGE_TEMPLATES[ $template ] ) ) {
				$mapped[] = LEGACY_PAGE_TEMPLATES[ $template ];
			}
			$mapped[] = $template;
		}
		return array_values( array_unique( $mapped ) );
	}
);
