<?php
/**
 * Value cell: icon, text, or both, with an optional toggletip.
 *
 * Icons are decorative SVGs with a visually hidden text alternative. The
 * tooltip is anchored to its toggle and opens on hover/focus through CSS;
 * view.js adds tap, Esc, outside click, viewport clamping and the flip above
 * the toggle.
 *
 * @var array $args {
 *     @type array $value icon (none|check|minus), text, tooltip (RichText, may be empty), tooltip_id.
 * }
 */

use Ihumbak\CompareTable\Renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$value = $args['value'];
?>
<div class="ihumbak-ct__value" role="cell">
	<?php if ( 'none' !== $value['icon'] ) : ?>
		<span class="ihumbak-ct__value-icon ihumbak-ct__value-icon--<?php echo esc_attr( $value['icon'] ); ?>">
			<?php echo Renderer::icon( $value['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="ihumbak-ct__sr"><?php echo 'check' === $value['icon'] ? esc_html__( 'Tak', 'ihumbak-compare-table' ) : esc_html__( 'Nie', 'ihumbak-compare-table' ); ?></span>
		</span>
	<?php endif; ?>
	<?php if ( '' !== $value['text'] ) : ?>
		<span class="ihumbak-ct__value-text"><?php echo wp_kses_post( $value['text'] ); ?></span>
	<?php endif; ?>
	<?php if ( '' !== $value['tooltip'] ) : ?>
		<span class="ihumbak-ct__tooltip-wrap">
			<button
				type="button"
				class="ihumbak-ct__tooltip-toggle"
				aria-label="<?php echo esc_attr__( 'Więcej informacji', 'ihumbak-compare-table' ); ?>"
				aria-expanded="false"
				aria-describedby="<?php echo esc_attr( $value['tooltip_id'] ); ?>"
			><?php echo Renderer::icon( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<span class="ihumbak-ct__tooltip" role="tooltip" id="<?php echo esc_attr( $value['tooltip_id'] ); ?>"><?php echo Renderer::kses_inline( $value['tooltip'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</span>
	<?php endif; ?>
</div>
