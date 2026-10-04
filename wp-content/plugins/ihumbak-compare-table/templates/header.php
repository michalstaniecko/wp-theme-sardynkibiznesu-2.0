<?php
/**
 * Header: one column header per service.
 *
 * The button and the price note are hidden by CSS in the narrow layout, where
 * templates/cta.php takes over.
 *
 * @var array $args {
 *     @type string $feature_label RichText label of the feature column, may be empty.
 *     @type array  $columns       Each: name, price, price_note (RichText), image_id, image_alt, button (array|null).
 * }
 */

use Ihumbak\CompareTable\Renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ihumbak-ct__header" role="rowgroup">
	<div class="ihumbak-ct__header-row" role="row">
		<div class="ihumbak-ct__corner<?php echo '' === $args['feature_label'] ? ' ihumbak-ct__corner--empty' : ''; ?>" role="columnheader">
			<?php if ( '' !== $args['feature_label'] ) : ?>
				<span class="ihumbak-ct__corner-label"><?php echo wp_kses_post( $args['feature_label'] ); ?></span>
			<?php else : ?>
				<span class="ihumbak-ct__sr"><?php esc_html_e( 'Funkcja', 'ihumbak-compare-table' ); ?></span>
			<?php endif; ?>
		</div>
		<?php foreach ( $args['columns'] as $column ) : ?>
			<div class="ihumbak-ct__column" role="columnheader">
				<?php
				if ( $column['image_id'] ) {
					$image_attrs = array( 'class' => 'ihumbak-ct__column-img' );

					// An empty alt in the block means "use the alt stored with the attachment".
					if ( '' !== $column['image_alt'] ) {
						$image_attrs['alt'] = $column['image_alt'];
					}

					$image = wp_get_attachment_image( $column['image_id'], 'medium', false, $image_attrs );

					if ( $image ) {
						echo '<div class="ihumbak-ct__column-image">' . $image . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
				?>
				<?php if ( '' !== $column['name'] ) : ?>
					<div class="ihumbak-ct__column-name"><?php echo wp_kses_post( $column['name'] ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== $column['price'] ) : ?>
					<div class="ihumbak-ct__column-price"><?php echo wp_kses_post( $column['price'] ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== $column['price_note'] ) : ?>
					<div class="ihumbak-ct__column-price-note"><?php echo wp_kses_post( $column['price_note'] ); ?></div>
				<?php endif; ?>
				<?php if ( $column['button'] ) : ?>
					<div class="ihumbak-ct__column-button">
						<?php echo Renderer::template( 'button', array( 'button' => $column['button'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
