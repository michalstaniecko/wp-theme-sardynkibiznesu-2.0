<?php
/**
 * Title: Nakładka menu — pasek górny
 * Slug: sardynkibiznesu-3-0/navigation-overlay-top
 * Categories: header
 * Inserter: no
 * Description: Logo i przycisk zamknięcia nakładki menu (w miejscu przycisku menu).
 *
 * Only the logo row is a pattern (theme asset URL resolved in PHP). The menu itself
 * must sit directly in parts/navigation-overlay.html: core reads that file with
 * parse_blocks() and marks only top-level core/navigation blocks as
 * `_isWithinOverlayTemplatePart`, without resolving core/pattern blocks.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_logo_url = get_theme_file_uri( 'assets/images/logo-sardynkibiznesu.png' );
$sb_home_url = home_url( '/' );
?>
<!-- wp:group {"className":"sb-mobile-nav__top","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group sb-mobile-nav__top">
	<!-- wp:image {"lightbox":{"enabled":false},"width":"182px","height":"32px","sizeSlug":"full","linkDestination":"custom","className":"sb-header__logo"} -->
	<figure class="wp-block-image size-full is-resized sb-header__logo"><a href="<?php echo esc_url( $sb_home_url ); ?>"><img src="<?php echo esc_url( $sb_logo_url ); ?>" alt="Sardynki Biznesu — strona główna" style="width:182px;height:32px"/></a></figure>
	<!-- /wp:image -->

	<!-- wp:navigation-overlay-close /-->
</div>
<!-- /wp:group -->
