<?php
/**
 * Root of the comparison table.
 *
 * A div grid with explicit ARIA table roles instead of a <table>: the rows
 * need display:grid for the narrow layout and the collapse animation, which
 * strips native table semantics in some browsers.
 *
 * @var array $args {
 *     @type string $wrapper_attributes Escaped HTML attributes of the root element.
 *     @type string $uid                Unique prefix for IDs of this table instance.
 *     @type int    $cols               Number of service columns (1-4).
 *     @type int    $heading_level      Heading level of section titles (2-6).
 *     @type string $caption            RichText, may be empty.
 *     @type string $feature_label      RichText, may be empty.
 *     @type array  $columns            Service columns.
 *     @type array  $sections           Sections with their rows.
 *     @type array  $buttons            One button (or null) per column.
 *     @type bool   $has_buttons        Whether any column has a button.
 * }
 */

use Ihumbak\CompareTable\Renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$caption_id = $args['uid'] . '-caption';
?>
<div <?php echo $args['wrapper_attributes']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the renderer. ?>>
	<?php if ( '' !== $args['caption'] ) : ?>
		<div class="ihumbak-ct__caption" id="<?php echo esc_attr( $caption_id ); ?>"><?php echo wp_kses_post( $args['caption'] ); ?></div>
	<?php endif; ?>
	<div
		class="ihumbak-ct__table"
		role="table"
		aria-colcount="<?php echo esc_attr( $args['cols'] + 1 ); ?>"
		<?php if ( '' !== $args['caption'] ) : ?>
			aria-labelledby="<?php echo esc_attr( $caption_id ); ?>"
		<?php else : ?>
			aria-label="<?php echo esc_attr__( 'Tabela porównawcza', 'ihumbak-compare-table' ); ?>"
		<?php endif; ?>
	>
		<?php
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- partials escape their own output.
		echo Renderer::template( 'header', $args );

		foreach ( $args['sections'] as $section ) {
			echo Renderer::template(
				'section',
				array(
					'section'       => $section,
					'cols'          => $args['cols'],
					'heading_level' => $args['heading_level'],
				)
			);
		}
		// phpcs:enable
		?>
	</div>
	<?php
	if ( $args['has_buttons'] ) {
		echo Renderer::template( 'cta', $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</div>
