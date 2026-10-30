<?php
/**
 * ihumbak/share-buttons (spec #36, decision #27 pkt 5): share the current post.
 *
 * - „Wyślij e-mailem”: a plain mailto: link, works without JS.
 * - „Kopiuj link” and „Udostępnij” (Web Share) are rendered `hidden`; the view
 *   module shows each one only when the browser has the API (clipboard,
 *   navigator.share). Without JS only the e-mail link is visible.
 *
 * @var WP_Block $block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ihumbak_post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
if ( ! $ihumbak_post_id ) {
	return;
}

$ihumbak_url   = get_permalink( $ihumbak_post_id );
$ihumbak_title = html_entity_decode( wp_strip_all_tags( get_the_title( $ihumbak_post_id ) ), ENT_QUOTES, 'UTF-8' );
$ihumbak_mail  = 'mailto:?subject=' . rawurlencode( $ihumbak_title ) . '&body=' . rawurlencode( $ihumbak_url );

$ihumbak_icon = static fn ( string $paths ): string => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths . '</svg>';

$ihumbak_icons = array(
	'link'  => $ihumbak_icon( '<path d="M10 13a5 5 0 0 0 7.07 0l3-3a5 5 0 0 0-7.07-7.07l-1.5 1.5"/><path d="M14 11a5 5 0 0 0-7.07 0l-3 3a5 5 0 0 0 7.07 7.07l1.5-1.5"/>' ),
	'mail'  => $ihumbak_icon( '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>' ),
	'share' => $ihumbak_icon( '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>' ),
);

wp_interactivity_state(
	'ihumbak/share-buttons',
	array(
		'canCopy'    => false,
		'canShare'   => false,
		'statusText' => '',
		'copiedText' => __( 'Link skopiowany', 'ihumbak-site-blocks' ),
	)
);

$ihumbak_context = array(
	'url'    => $ihumbak_url,
	'title'  => $ihumbak_title,
	'copied' => false,
);
?>
<div <?php echo get_block_wrapper_attributes(); ?> data-wp-interactive="ihumbak/share-buttons" <?php echo wp_interactivity_data_wp_context( $ihumbak_context ); ?> data-wp-init="callbacks.detect">
	<span class="wp-block-ihumbak-share-buttons__label"><?php esc_html_e( 'Udostępnij:', 'ihumbak-site-blocks' ); ?></span>
	<button type="button" class="wp-block-ihumbak-share-buttons__button" aria-label="<?php esc_attr_e( 'Kopiuj link', 'ihumbak-site-blocks' ); ?>" hidden
		data-wp-bind--hidden="!state.canCopy"
		data-wp-on--click="actions.copyLink"
	><?php echo $ihumbak_icons['link']; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
	<a class="wp-block-ihumbak-share-buttons__button" href="<?php echo esc_url( $ihumbak_mail, array( 'mailto' ) ); ?>" aria-label="<?php esc_attr_e( 'Wyślij e-mailem', 'ihumbak-site-blocks' ); ?>"><?php echo $ihumbak_icons['mail']; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
	<button type="button" class="wp-block-ihumbak-share-buttons__button" aria-label="<?php esc_attr_e( 'Udostępnij', 'ihumbak-site-blocks' ); ?>" hidden
		data-wp-bind--hidden="!state.canShare"
		data-wp-on--click="actions.share"
	><?php echo $ihumbak_icons['share']; // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	<span class="wp-block-ihumbak-share-buttons__status" role="status" aria-live="polite" data-wp-text="state.statusText"></span>
</div>
