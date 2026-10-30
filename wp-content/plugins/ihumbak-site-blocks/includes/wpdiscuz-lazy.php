<?php
/**
 * wpDiscuz assets loaded when the reader approaches the comments (#70, spec #36: budget of
 * a post with comments open). The comments stay in the HTML; only the plugin's CSS and JS
 * wait, together with jQuery when nothing else on the page needs it.
 *
 * wpDiscuz's own `firstLoadWithAjax` is not an option: it removes the comments from the HTML
 * and leaves the assets in place. WordPress prints the deferred handles itself, as late as
 * possible, so its dependency resolver decides what is left: whatever the page has already
 * printed (jQuery for YASR) is skipped, and the versions, `wp_localize_script` data and inline
 * parts come out exactly as wpDiscuz enqueued them. Browser side: assets/wpdiscuz-lazy.js.
 *
 * RISK: depends on wpDiscuz naming its handles and paths this way, and on what its stylesheets
 * hide (CRITICAL_CSS). Re-check after every wpDiscuz update.
 */

namespace Ihumbak\SiteBlocks\WpdiscuzLazy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** After wpDiscuz and its add-ons have enqueued, and after everything else has printed. */
const LATE_PRIORITY = 1000;

/**
 * Until the stylesheets arrive: what only they hide stays hidden (a11y), icons don't fill the
 * column and the section keeps roughly its final height on a post without comments (CLS).
 * `:where()` so that wpDiscuz's own rules always win: without JS this stays next to its CSS.
 */
const CRITICAL_CSS = ':where(#wpd-editor-source-code-wrapper,#wpd-editor-source-code-wrapper-bg,#wpdcom .wpdiscuz-hidden,#wpdcom .wpdiscuz-sort-buttons,.lity-hide){display:none}:where(#wpdcom .wpd-comment-share){position:absolute;visibility:hidden}:where(#wpdcom svg){max-width:32px;max-height:32px}:where(#wpdcom){min-height:400px}';

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\defer_assets', LATE_PRIORITY );

function defer_assets(): void {
	if ( ! defined( 'WPDISCUZ_DIR_NAME' ) ) {
		return;
	}

	$scripts = own_handles( wp_scripts() );

	// The inline feedback shortcode works in the article, far from the comments.
	if ( ! $scripts || has_shortcode( (string) get_post_field( 'post_content' ), 'wpdiscuz-feedback' ) ) {
		return;
	}

	$styles = own_handles( wp_styles() );

	array_walk( $scripts, 'wp_dequeue_script' );
	array_walk( $styles, 'wp_dequeue_style' );

	wp_register_style( 'ihumbak-wpdiscuz-critical', false );
	wp_enqueue_style( 'ihumbak-wpdiscuz-critical' );
	wp_add_inline_style( 'ihumbak-wpdiscuz-critical', CRITICAL_CSS );

	$asset = require plugin_dir_path( IHUMBAK_SITE_BLOCKS_FILE ) . 'assets/wpdiscuz-lazy.asset.php';
	wp_enqueue_script(
		'ihumbak-wpdiscuz-lazy',
		plugins_url( 'assets/wpdiscuz-lazy.js', IHUMBAK_SITE_BLOCKS_FILE ),
		$asset['dependencies'],
		$asset['version'],
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// After wp_print_styles (8), so a dependency the page needs anyway has been printed.
	add_action(
		'wp_head',
		static function () use ( $styles ): void {
			printf( '<noscript id="ihumbak-wpdiscuz-styles">%s</noscript>' . "\n", captured( wp_styles(), $styles ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup printed by WordPress.
		},
		9
	);

	add_action(
		'wp_footer',
		static function () use ( $scripts ): void {
			printf( '<template id="ihumbak-wpdiscuz-scripts">%s</template>' . "\n", captured( wp_scripts(), $scripts ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup printed by WordPress.
		},
		LATE_PRIORITY
	);
}

/**
 * Enqueued handles of wpDiscuz, in queue order. Core's `quicktags` counts when wpDiscuz is what
 * asks for it (mobile): it has no dependants, so a script that needs it would still pull it in.
 *
 * @return string[]
 */
function own_handles( \WP_Dependencies $assets ): array {
	$own = array();

	foreach ( $assets->queue as $handle ) {
		$src = (string) ( $assets->registered[ $handle ]->src ?? '' );

		if (
			str_starts_with( $handle, 'wpdiscuz-' )
			|| str_contains( $src, '/' . WPDISCUZ_DIR_NAME . '/' )
			|| ( 'quicktags' === $handle && in_array( 'wpdiscuz-quicktags', $assets->queue, true ) )
		) {
			$own[] = $handle;
		}
	}

	return $own;
}

/**
 * @param string[] $handles
 * @return string What WordPress prints for the handles and their unprinted dependencies, in order.
 */
function captured( \WP_Dependencies $assets, array $handles ): string {
	ob_start();
	$assets->do_items( $handles );
	return (string) ob_get_clean();
}
