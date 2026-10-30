<?php
/**
 * Render of the legacy `acf/sb-button` block: the ACF `data` attribute → Przycisk.
 *
 * @var array $attributes Block attributes (`data` in ACF format, `align`).
 *
 * @package ihumbak-content-elements
 */

defined( 'ABSPATH' ) || exit;

echo Ihumbak\ContentElements\Button\acf_block_markup( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in render().
