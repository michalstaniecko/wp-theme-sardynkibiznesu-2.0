<?php
/**
 * Section: an optional heading bar with a toggle, and the feature rows.
 *
 * The initial state is rendered here (class + aria-expanded); view.js only
 * toggles it and adds `inert` to closed bodies. `inert` is deliberately not
 * printed by PHP: without JavaScript it could not be removed, while the
 * <noscript> style (Renderer::print_noscript_style(), once per page in the
 * footer) forces every section open.
 *
 * A section without a title has no heading bar and cannot be collapsed.
 *
 * @var array $args {
 *     @type array $section       title (RichText), collapsible, open, body_id, rows.
 *     @type int   $cols          Number of service columns.
 *     @type int   $heading_level 2-6.
 * }
 */

use Ihumbak\CompareTable\Renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section = $args['section'];
$tag     = 'h' . (int) $args['heading_level'];

$classes = 'ihumbak-ct__section';
$classes .= $section['open'] ? ' is-open' : '';
$classes .= $section['collapsible'] ? '' : ' ihumbak-ct__section--static';
?>
<div class="<?php echo esc_attr( $classes ); ?>" role="rowgroup">
	<?php if ( $section['collapsible'] ) : ?>
		<div class="ihumbak-ct__section-head" role="row">
			<div class="ihumbak-ct__section-head-cell" role="cell" aria-colspan="<?php echo esc_attr( $args['cols'] + 1 ); ?>">
				<<?php echo tag_escape( $tag ); ?> class="ihumbak-ct__section-heading">
					<button
						type="button"
						class="ihumbak-ct__section-toggle"
						aria-expanded="<?php echo $section['open'] ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr( $section['body_id'] ); ?>"
					>
						<span class="ihumbak-ct__section-title"><?php echo Renderer::kses_inline( $section['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="ihumbak-ct__section-toggle-icon"><?php echo Renderer::icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</button>
				</<?php echo tag_escape( $tag ); ?>>
			</div>
		</div>
	<?php endif; ?>
	<div class="ihumbak-ct__section-body" id="<?php echo esc_attr( $section['body_id'] ); ?>" role="presentation">
		<div class="ihumbak-ct__section-body-inner" role="presentation">
			<?php
			foreach ( $section['rows'] as $row ) {
				echo Renderer::template( 'row', array( 'row' => $row ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	</div>
</div>
