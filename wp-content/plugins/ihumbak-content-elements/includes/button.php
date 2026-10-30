<?php
/**
 * Przycisk (GLOSSARY): `core/button` with block styles as variants.
 *
 * New content uses `core/button` and picks a variant by block style. The three legacy
 * notations — `[sb_button]`, `[cmsmasters_button]` and the ACF block `acf/sb-button` —
 * render the very same `core/buttons` > `core/button` markup, so one CSS (in the theme)
 * styles them all. The database is never changed.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\Button;

defined( 'ABSPATH' ) || exit;

/** Variant styles of `core/button`: slug => label. `outline` overrides the core label. */
const STYLES = array(
	'outline'  => 'Obrys',
	'accent'   => 'Akcent',
	'download' => 'Ikona pobierania',
);

/** Label of legacy buttons saved without one (as in 2.0). */
const DEFAULT_LABEL = 'Sprawdź zawartość kursu';

/**
 * ACF field names of `acf/sb-button` => field keys (2.0 `acf-fields/group_618e5715c16d0.json`).
 * `data` normally holds values under the names; the keys are a fallback for the
 * "by key" format ACF also writes.
 */
const ACF_FIELDS = array(
	'sb-block__button--label' => 'field_618e572531fa2',
	'sb-block__button--url'   => 'field_618e573031fa3',
	'sb-block__url-outer'     => 'field_61df40c2a9b6b',
	'sb-block__button-color'  => 'field_65a5aa5ff7a82',
);

add_action( 'init', __NAMESPACE__ . '\\register_styles' );
add_action( 'init', __NAMESPACE__ . '\\register_acf_block' );
add_shortcode( 'sb_button', __NAMESPACE__ . '\\sb_button_shortcode' );
add_shortcode( 'cmsmasters_button', __NAMESPACE__ . '\\cmsmasters_button_shortcode' );

/**
 * Registers the Przycisk variants as block styles of `core/button` and the plugin's
 * minimal CSS (download icon). Colours and outline come from the theme.
 */
function register_styles(): void {
	// Loaded only on pages that render a `core/button` (legacy notations included).
	wp_enqueue_block_style(
		'core/button',
		array(
			'handle' => 'ihumbak-content-elements-button',
			'src'    => plugins_url( 'assets/button.css', IHUMBAK_CONTENT_ELEMENTS_FILE ),
			'path'   => dirname( IHUMBAK_CONTENT_ELEMENTS_FILE ) . '/assets/button.css',
			'ver'    => IHUMBAK_CONTENT_ELEMENTS_VERSION,
		)
	);

	foreach ( STYLES as $name => $label ) {
		register_block_style(
			'core/button',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}

/**
 * Renders one Przycisk exactly as the block editor saves `core/buttons` > `core/button`
 * (`wp.blocks.serialize`), then runs it through the block renderer, so the output, the
 * layout classes and the enqueued core CSS are the same as for a native button.
 *
 * @param string $text    Button text, already escaped HTML.
 * @param string $url     Link target (escaped here).
 * @param string $style   Block style slug from STYLES, or '' for the default (fill).
 * @param string $justify Justification of `core/buttons`: left|center|right, or '' for none.
 * @param string $target  `target` attribute, e.g. '_blank', or ''.
 * @param string $rel     `rel` attribute, or ''.
 */
function render( string $text, string $url, string $style = '', string $justify = '', string $target = '', string $rel = '' ): string {
	$buttons_attrs = '';
	if ( in_array( $justify, array( 'left', 'center', 'right' ), true ) ) {
		$buttons_attrs = wp_json_encode(
			array(
				'layout' => array(
					'type'           => 'flex',
					'justifyContent' => $justify,
				),
			)
		) . ' ';
	}

	$class        = isset( STYLES[ $style ] ) ? 'is-style-' . $style : '';
	$button_attrs = $class ? wp_json_encode( array( 'className' => $class ) ) . ' ' : '';

	// esc_url() would write `&` as `&#038;`; the editor saves `&amp;`.
	$link = '<a class="wp-block-button__link wp-element-button" href="' . esc_attr( esc_url_raw( $url ) ) . '"';
	if ( '' !== $target ) {
		$link .= ' target="' . esc_attr( $target ) . '"';
	}
	if ( '' !== $rel ) {
		$link .= ' rel="' . esc_attr( $rel ) . '"';
	}
	$link .= '>' . $text . '</a>';

	$markup = '<!-- wp:buttons ' . $buttons_attrs . "-->\n"
		. '<div class="wp-block-buttons"><!-- wp:button ' . $button_attrs . "-->\n"
		. '<div class="' . esc_attr( trim( 'wp-block-button ' . $class ) ) . '">' . $link . "</div>\n"
		. "<!-- /wp:button --></div>\n"
		. '<!-- /wp:buttons -->';

	return do_blocks( $markup );
}

/**
 * `[sb_button url="…" label="…" rel="…" /]` → Przycisk with the outline style,
 * opened in a new tab, `rel="nofollow"` by default (as in 2.0).
 *
 * @param array|string $atts Shortcode attributes.
 */
function sb_button_shortcode( $atts ): string {
	$a = shortcode_atts(
		array(
			'url'   => '#',
			'label' => DEFAULT_LABEL,
			'rel'   => 'nofollow',
		),
		$atts,
		'sb_button'
	);

	$label = '' !== trim( (string) $a['label'] ) ? (string) $a['label'] : DEFAULT_LABEL;

	return render( esc_html( $label ), (string) $a['url'], 'outline', '', '_blank', (string) $a['rel'] );
}

/**
 * `[cmsmasters_button button_link="…" button_target="…" button_text_align="…"]Text[/cmsmasters_button]`
 * → Przycisk with the download-icon style (2.0 always showed the download icon).
 * `button_text_align` becomes the justification; `button_target="blank"` opens a new tab
 * (in content it is always "self", which 2.0 printed literally as a window name).
 * Typography, colour, icon and animation attributes are ignored, as in 2.0.
 *
 * @param array|string $atts    Shortcode attributes.
 * @param string|null  $content Button text.
 */
function cmsmasters_button_shortcode( $atts, $content = null ): string {
	$a = shortcode_atts(
		array(
			'button_link'       => '#',
			'button_target'     => '',
			'button_text_align' => '',
		),
		$atts,
		'cmsmasters_button'
	);

	$target = in_array( $a['button_target'], array( 'blank', '_blank' ), true ) ? '_blank' : '';
	$text   = trim( wp_kses( (string) $content, 'data' ) );

	return render( $text, (string) $a['button_link'], 'download', (string) $a['button_text_align'], $target );
}

/**
 * Registers the dynamic block under its old name, so content saved with ACF renders
 * without ACF and without migration. Skipped while ACF itself registers the name (2.0).
 */
function register_acf_block(): void {
	if ( \WP_Block_Type_Registry::get_instance()->is_registered( 'acf/sb-button' ) ) {
		return;
	}
	register_block_type( dirname( IHUMBAK_CONTENT_ELEMENTS_FILE ) . '/blocks/sb-button' );
}

/**
 * Reads one field of the ACF `data` attribute, by field name or by field key.
 *
 * @param array  $data ACF block data.
 * @param string $name Field name from ACF_FIELDS.
 * @return mixed
 */
function acf_field( array $data, string $name ) {
	return $data[ $name ] ?? $data[ ACF_FIELDS[ $name ] ] ?? null;
}

/**
 * `acf/sb-button` → Przycisk: external URL, or the permalink of the related post computed
 * at render time; colour "blue" → accent style, otherwise the default fill; block `align`
 * → justification; new tab with `rel="nofollow"` (as in 2.0).
 *
 * @param array $attributes Block attributes.
 */
function acf_block_markup( array $attributes ): string {
	$data = is_array( $attributes['data'] ?? null ) ? $attributes['data'] : array();

	$label = trim( (string) acf_field( $data, 'sb-block__button--label' ) );
	$url   = trim( (string) acf_field( $data, 'sb-block__url-outer' ) );

	if ( '' === $url ) {
		$related = acf_field( $data, 'sb-block__button--url' );
		$post_id = (int) ( is_array( $related ) ? reset( $related ) : $related );
		$url     = $post_id ? (string) get_permalink( $post_id ) : '';
	}

	$style = 'blue' === acf_field( $data, 'sb-block__button-color' ) ? 'accent' : '';

	return render(
		esc_html( '' !== $label ? $label : DEFAULT_LABEL ),
		'' !== $url ? $url : '#',
		$style,
		(string) ( $attributes['align'] ?? '' ),
		'_blank',
		'nofollow'
	);
}
