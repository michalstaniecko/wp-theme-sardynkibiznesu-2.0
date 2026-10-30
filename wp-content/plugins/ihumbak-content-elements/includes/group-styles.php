<?php
/**
 * Notka and Ramka promocyjna (GLOSSARY): `core/group` with block styles.
 *
 * The editor inserts a group and picks a style: `neutral` is the Notka (a warning or
 * remark, any blocks inside: paragraphs, lists), `accent` and `inverse` are the two
 * variants of the Ramka promocyjna. The look lives in the theme (ADR-0001).
 *
 * The legacy `[cmsmasters_notice]` renders the very markup the editor saves for a Notka
 * with paragraphs, so old and new Notki look the same. The database is never changed.
 *
 * @package ihumbak-content-elements
 */

namespace Ihumbak\ContentElements\GroupStyles;

defined( 'ABSPATH' ) || exit;

/** Block styles of `core/group`: slug => label. */
const STYLES = array(
	'neutral' => 'Notka',
	'accent'  => 'Ramka promocyjna: akcent',
	'inverse' => 'Ramka promocyjna: odwrócona',
);

add_action( 'init', __NAMESPACE__ . '\\register_styles' );
add_shortcode( 'cmsmasters_notice', __NAMESPACE__ . '\\cmsmasters_notice_shortcode' );
// After wpautop and shortcode_unautop (both at 10, registered earlier), before do_shortcode (11).
add_filter( 'the_content', __NAMESPACE__ . '\\unwrap_notice_tags', 10 );

/**
 * Registers the Notka and Ramka promocyjna styles of `core/group`.
 */
function register_styles(): void {
	foreach ( STYLES as $name => $label ) {
		register_block_style(
			'core/group',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}

/**
 * Renders a Notka exactly as the block editor saves `core/group` (style `neutral`) with
 * paragraph blocks inside, then runs it through the block renderer, so the output and
 * the layout classes are the same as for a native Notka.
 *
 * @param string[] $paragraphs Inner HTML of each paragraph (already safe post content).
 */
function render_notka( array $paragraphs ): string {
	$inner = array_map(
		static fn( string $html ): string => "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->",
		$paragraphs
	);

	$markup = '<!-- wp:group {"className":"is-style-neutral","layout":{"type":"constrained"}} -->' . "\n"
		. '<div class="wp-block-group is-style-neutral">' . implode( "\n\n", $inner ) . "</div>\n"
		. '<!-- /wp:group -->';

	return do_blocks( $markup );
}

/**
 * Splits the legacy tag content into paragraphs. By now the content went through wpautop
 * (classic posts, shortcode blocks) or sits in paragraph blocks, so it is a mix of `<p>`
 * elements and loose text; stray `</p>` / `<p>` at the edges are left by tags placed
 * inside paragraphs. In published content it holds only inline markup.
 *
 * @param string $content Shortcode content.
 * @return string[]
 */
function paragraphs( string $content ): array {
	$text = preg_replace( '#<p(?:\s[^>]*)?>#i', "\n\n", $content );
	$text = str_ireplace( '</p>', "\n\n", $text );

	$out = array();
	foreach ( preg_split( '/\n\s*\n/', $text ) as $chunk ) {
		$chunk = trim( preg_replace( '#(<br\s*/?>\s*)+$#i', '', trim( $chunk ) ) );
		if ( '' !== $chunk && '&nbsp;' !== $chunk ) {
			$out[] = $chunk;
		}
	}
	return $out;
}

/**
 * `[cmsmasters_notice …]Text[/cmsmasters_notice]` → Notka. All attributes (type, icon,
 * colours, close, animation) are ignored, as in 2.0.
 *
 * @param array|string $atts    Shortcode attributes (ignored).
 * @param string|null  $content Notka text.
 */
function cmsmasters_notice_shortcode( $atts, $content = null ): string {
	$paragraphs = paragraphs( (string) $content );
	return $paragraphs ? render_notka( $paragraphs ) : '';
}

/**
 * Takes the legacy opening and closing tags out of the paragraphs wpautop or the editor
 * put them in (`<p>[cmsmasters_notice …]</p>`, `<p>[/cmsmasters_notice]</p>`), so the
 * Notka is not nested in a `<p>` and leaves no empty paragraphs around it.
 *
 * @param string $content Post content.
 * @return string
 */
function unwrap_notice_tags( $content ) {
	global $shortcode_tags;

	// While the 2.0 theme overrides the tag with its own handler, keep its output untouched.
	$ours = __NAMESPACE__ . '\\cmsmasters_notice_shortcode';
	if ( ! is_string( $content ) || false === strpos( $content, 'cmsmasters_notice' ) || ( $shortcode_tags['cmsmasters_notice'] ?? null ) !== $ours ) {
		return $content;
	}
	return preg_replace( '#<p(?:\s[^>]*)?>\s*(\[/?cmsmasters_notice\b[^\]]*\])\s*</p>#i', '$1', $content );
}
