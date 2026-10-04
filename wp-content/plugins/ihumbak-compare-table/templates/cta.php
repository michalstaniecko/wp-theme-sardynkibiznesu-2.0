<?php
/**
 * Row of per-service buttons under the table.
 *
 * Shown only in the narrow layout, where the header drops its buttons. The
 * cells line up with the service columns, so a column without a button keeps
 * an empty slot.
 *
 * @var array $args {
 *     @type array $buttons One button array (or null) per service column.
 * }
 */

use Ihumbak\CompareTable\Renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ihumbak-ct__cta">
	<?php foreach ( $args['buttons'] as $button ) : ?>
		<div class="ihumbak-ct__cta-item">
			<?php
			if ( $button ) {
				echo Renderer::template( 'button', array( 'button' => $button ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	<?php endforeach; ?>
</div>
