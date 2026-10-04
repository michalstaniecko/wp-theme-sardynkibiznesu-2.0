<?php
/**
 * Feature row: a row header and one value cell per service column.
 *
 * The variant follows from which fields are filled:
 * - title + description: the description is collapsible (native <details>, no JS);
 * - description only:    always visible;
 * - title only:          plain label.
 *
 * @var array $args {
 *     @type array $row title, description (RichText, may be empty), values (already padded/truncated to the column count).
 * }
 */

use Ihumbak\CompareTable\Renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$row = $args['row'];
?>
<div class="ihumbak-ct__row" role="row">
	<div class="ihumbak-ct__feature" role="rowheader">
		<?php if ( '' !== $row['title'] && '' !== $row['description'] ) : ?>
			<details class="ihumbak-ct__feature-details">
				<summary class="ihumbak-ct__feature-title"><?php echo wp_kses_post( $row['title'] ); ?></summary>
				<div class="ihumbak-ct__feature-description"><?php echo wp_kses_post( $row['description'] ); ?></div>
			</details>
		<?php elseif ( '' !== $row['title'] ) : ?>
			<div class="ihumbak-ct__feature-title"><?php echo wp_kses_post( $row['title'] ); ?></div>
		<?php elseif ( '' !== $row['description'] ) : ?>
			<div class="ihumbak-ct__feature-description"><?php echo wp_kses_post( $row['description'] ); ?></div>
		<?php endif; ?>
	</div>
	<?php
	foreach ( $row['values'] as $value ) {
		echo Renderer::template( 'value', array( 'value' => $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</div>
