<?php
/**
 * Service link, shared by the header and the CTA row.
 *
 * A plain underlined text link: the colour is the theme's link colour, the
 * underline comes from the plugin's stylesheet (.ihumbak-ct__button).
 *
 * @var array $args {
 *     @type array $button url (already esc_url'd), text (RichText), new_tab, rel, service (plain text).
 * }
 */

use Ihumbak\CompareTable\Renderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button = $args['button'];
?>
<a
	class="ihumbak-ct__button"
	href="<?php echo esc_url( $button['url'] ); ?>"
	<?php if ( $button['new_tab'] ) : ?>
		target="_blank"
	<?php endif; ?>
	<?php if ( '' !== $button['rel'] ) : ?>
		rel="<?php echo esc_attr( $button['rel'] ); ?>"
	<?php endif; ?>
><?php echo Renderer::kses_inline( $button['text'] ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped; ?><?php if ( '' !== $button['service'] ) : ?><span class="ihumbak-ct__sr"> &ndash; <?php echo esc_html( $button['service'] ); ?></span><?php endif; ?><?php if ( $button['new_tab'] ) : ?><span class="ihumbak-ct__sr"> <?php esc_html_e( '(otwiera się w nowej karcie)', 'ihumbak-compare-table' ); ?></span><?php endif; ?></a>
