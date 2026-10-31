<?php
/**
 * Title: Nagłówek
 * Slug: sardynkibiznesu-3-0/header
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 * Description: Przyklejony nagłówek: logo, menu (hover, poniżej 1024 px nakładka), przycisk wyszukiwania i pasek wyszukiwania z popularnymi hasłami.
 *
 * The header part embeds this pattern so that theme asset URLs are resolved in PHP.
 * Blocks ihumbak/site-header, ihumbak/search-toggle and ihumbak/search-bar come from
 * the ihumbak-site-blocks plugin (ADR-0001). The navigation has no `ref`: both
 * navigation blocks (here and in the navigation-overlay part) fall back to the same,
 * most recent wp_navigation menu; picking a menu in the Site Editor stores its `ref`.
 *
 * @package sardynkibiznesu-3.0
 */

defined( 'ABSPATH' ) || exit;

$sb_logo_url = get_theme_file_uri( 'assets/images/logo-sardynkibiznesu.png' );
$sb_home_url = home_url( '/' );
?>
<!-- wp:ihumbak/site-header -->
<!-- wp:group {"className":"sb-header__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group sb-header__inner">
	<!-- wp:image {"lightbox":{"enabled":false},"width":"182px","height":"32px","sizeSlug":"full","linkDestination":"custom","className":"sb-header__logo"} -->
	<figure class="wp-block-image size-full is-resized sb-header__logo"><a href="<?php echo esc_url( $sb_home_url ); ?>"><img src="<?php echo esc_url( $sb_logo_url ); ?>" alt="Sardynki Biznesu — strona główna" style="width:182px;height:32px"/></a></figure>
	<!-- /wp:image -->

	<!-- wp:navigation {"overlay":"navigation-overlay","overlayMenu":"mobile","icon":"menu","submenuVisibility":"hover","className":"sb-nav","ariaLabel":"Menu główne","layout":{"type":"flex","flexWrap":"nowrap"}} /-->

	<!-- wp:group {"className":"sb-header__actions","layout":{"type":"flex","flexWrap":"nowrap"}} -->
	<div class="wp-block-group sb-header__actions">
		<!-- wp:ihumbak/search-toggle /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:ihumbak/search-bar -->
<!-- wp:search {"label":"Szukaj na blogu","showLabel":false,"placeholder":"Szukaj na blogu…","buttonText":"Szukaj","className":"sb-search-bar__form"} /-->

<!-- wp:group {"className":"sb-search-bar__hints","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group sb-search-bar__hints">
	<!-- wp:paragraph -->
	<p>Popularne:</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"className":"is-style-badges"} -->
	<div class="wp-block-buttons is-style-badges"><!-- wp:button -->
	<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/?s=RODO">RODO</a></div>
	<!-- /wp:button -->

	<!-- wp:button -->
	<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/?s=umowa+B2B">Umowa B2B</a></div>
	<!-- /wp:button -->

	<!-- wp:button -->
	<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/?s=faktura">Faktura</a></div>
	<!-- /wp:button -->

	<!-- wp:button -->
	<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/?s=regulamin+sklepu">Regulamin sklepu</a></div>
	<!-- /wp:button --></div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
<!-- /wp:ihumbak/search-bar -->
<!-- /wp:ihumbak/site-header -->
