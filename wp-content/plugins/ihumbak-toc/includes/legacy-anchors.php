<?php
/**
 * Kotwice v1 and the old Spis treści: [ihumbak_anchor] and [ihumbak_anchor_menu] from the
 * ihumbak-anchor plugin (2.0), rendered on the server.
 *
 * 2.0 printed the anchors as headings and let jQuery give them ids and build the menu in the
 * browser. Here the shortcodes print the headings, and a late the_content pass (after
 * shortcodes, wpautop and wptexturize, so on the same text the browser saw) gives every
 * .ihumbak-anchor heading its id and swaps the menu placeholder for the Spis treści.
 *
 * Like the jQuery, everything with the class ihumbak-anchor counts as a Kotwica v1: shortcode
 * output, headings pasted into the content with that class, and the two anchor-heading blocks
 * once they are converted by hand to core/heading. Give the converted heading the extra CSS
 * class "ihumbak-anchor" so it stays in the Spis treści.
 */

namespace Ihumbak\Toc\V1;

use function Ihumbak\Toc\render_toc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ANCHOR_CLASS     = 'ihumbak-anchor';
const MENU_PLACEHOLDER = '<!-- ihumbak-toc:v1-menu -->';
// Regex: the attributes of a tag whose class list contains ihumbak-anchor.
const ANCHOR_ATTRS = '\s[^>]*\bclass=["\'](?:[^"\']*\s)?ihumbak-anchor(?:\s[^"\']*)?["\'][^>]*';
// Regex: markup with no visible text: spaces (incl. no-break), inline tags, comments such as ad markers (<!--Ads2-->).
const INLINE_JUNK = '(?:\s|\x{00A0}|&nbsp;|&\#160;|<!--.*?-->|</?(?:span|strong|b|em|i|u|mark)\b[^>]*>)*';

add_shortcode( 'ihumbak_anchor', __NAMESPACE__ . '\render_anchor' );
add_shortcode( 'ihumbak_anchor_menu', __NAMESPACE__ . '\render_menu' );
// After do_shortcode (11), wp_filter_content_tags (12) and convert_smilies (20).
add_filter( 'the_content', __NAMESPACE__ . '\finish_content', 25 );

/**
 * [ihumbak_anchor type="h3" align="center"]Text[/ihumbak_anchor] → <h3 class="ihumbak-anchor has-text-align-center">Text</h3>
 */
function render_anchor( $atts, $content = '' ): string {
	$atts = shortcode_atts(
		array(
			'type'  => 'h2',
			'align' => 'left',
		),
		$atts,
		'ihumbak_anchor'
	);

	$tag = strtolower( trim( (string) $atts['type'] ) );
	if ( ! preg_match( '/^h[1-6]$/', $tag ) ) {
		$tag = 'h2';
	}

	$class = ANCHOR_CLASS;
	$align = strtolower( trim( (string) $atts['align'] ) );
	if ( in_array( $align, array( 'center', 'right' ), true ) ) {
		$class .= ' has-text-align-' . $align;
	}

	return sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $tag, $class, (string) $content );
}

/**
 * [ihumbak_anchor_menu] → placeholder; finish_content() replaces it once all anchors are known.
 */
function render_menu(): string {
	return MENU_PLACEHOLDER;
}

/**
 * Id of a v1 Kotwica, character for character what the 2.0 jQuery gave it:
 * text.replace(/\s/g, '-').replace(/[^a-zA-Z0-9\-]/g, '')
 *
 * @param string $text Heading text as the browser sees it (tags stripped, entities decoded).
 */
function anchor_id( string $text ): string {
	// JavaScript's \s, which unlike PCRE's also matches no-break and other Unicode spaces.
	$text = preg_replace( '/[\t\n\x{0B}\f\r \x{00A0}\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}\x{FEFF}]/u', '-', $text );

	return (string) preg_replace( '/[^a-zA-Z0-9\-]/', '', (string) $text );
}

/**
 * The text a browser would expose as textContent of an HTML fragment.
 */
function text_content( string $html ): string {
	return html_entity_decode( strip_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}

/**
 * the_content, late: unwraps old anchors, gives anchors their ids, prints the Spis treści.
 */
function finish_content( $content ) {
	if ( ! is_string( $content ) || ! str_contains( $content, ANCHOR_CLASS ) && ! str_contains( $content, MENU_PLACEHOLDER ) ) {
		return $content;
	}

	$original = $content;
	$content  = unwrap_anchors( $content );
	$has_menu = str_contains( $content, MENU_PLACEHOLDER );
	$headings = array();

	$content = preg_replace_callback(
		// Group 5: an empty heading right after the anchor, left where the closing tag sat in its own
		// heading: <h3>[ihumbak_anchor type="h3"]…</h3><h3>[/ihumbak_anchor]</h3>. It is dropped.
		'#<(h([1-6]))(' . ANCHOR_ATTRS . ')>(.*?)</\1\s*>(\s*<(h[1-6])(?:\s[^>]*)?>' . INLINE_JUNK . '</\6>)?#isu',
		static function ( array $m ) use ( $has_menu, &$headings ): string {
			$tag = new \WP_HTML_Tag_Processor( '<' . $m[1] . $m[3] . '>' );
			if ( ! $tag->next_tag() || ! $tag->has_class( ANCHOR_CLASS ) ) {
				return $m[0];
			}

			$text = text_content( $m[4] );
			$id   = $tag->get_attribute( 'id' );
			// On pages with the v1 menu jQuery overwrote every anchor id; elsewhere it never ran.
			if ( $has_menu || ! is_string( $id ) || '' === $id ) {
				$new_id = anchor_id( $text );
				if ( '' !== $new_id ) {
					$tag->set_attribute( 'id', $new_id );
					$id = $new_id;
				}
			}

			$headings[] = array(
				'level' => (int) $m[2],
				'id'    => is_string( $id ) ? $id : '',
				'text'  => trim( (string) preg_replace( '/\s+/u', ' ', $text ) ),
			);

			return $tag->get_updated_html() . $m[4] . '</' . $m[1] . '>' . ( empty( $m[5] ) ? '' : "\n" );
		},
		$content
	);
	// A PCRE failure (backtrack limit) returns null: better unprocessed content than none.
	if ( null === $content ) {
		return $original;
	}

	if ( $has_menu ) {
		$toc = render_toc( $headings );
		// A menu shortcode alone in a paragraph must not leave <p><nav></nav></p> behind.
		$content = (string) preg_replace( '#<p>\s*' . preg_quote( MENU_PLACEHOLDER, '#' ) . '\s*</p>#i', MENU_PLACEHOLDER, $content );
		$content = str_replace( MENU_PLACEHOLDER, $toc, $content );
	}

	return $content;
}

/**
 * Most anchors stand in the content as <hX>[ihumbak_anchor type=hY]…[/ihumbak_anchor]</hX>,
 * often with inline wrappers, sometimes misnested: <h2><span style="…">[ihumbak_anchor]…[/ihumbak_anchor]</span></h2>,
 * <h2><strong>[ihumbak_anchor]</strong>…[/ihumbak_anchor]</h2>. The browser showed an empty <hX>
 * or a heading inside a heading. Leave one heading at the anchor's level, with the inline tags
 * moved inside it (the parser closes whatever is left open at </hY>). An id on the dropped
 * wrapper is kept on an empty span at the start of the heading, so links to it land in the same place.
 */
function unwrap_anchors( string $content ): string {
	$unwrapped = preg_replace_callback(
		'#<(h[1-6]|p)(\s[^>]*)?>(' . INLINE_JUNK . ')<(h[1-6])(' . ANCHOR_ATTRS . ')>(.*?)</\4>(' . INLINE_JUNK . ')</\1>#isu',
		static function ( array $m ): string {
			[ , , $outer_attrs, $opens, $level, $anchor_attrs, $inner, $closes ] = $m;

			$kept_id = '';
			$outer   = new \WP_HTML_Tag_Processor( '<div' . $outer_attrs . '>' );
			if ( $outer->next_tag() ) {
				$id = $outer->get_attribute( 'id' );
				if ( is_string( $id ) && '' !== $id ) {
					$kept_id = '<span id="' . esc_attr( $id ) . '"></span>';
				}
			}

			// Spaces around the wrappers were outside the anchor heading; keep them out of its text (and id).
			$opens  = (string) preg_replace( '/(^|>)[^<]+/', '$1', $opens );
			$closes = (string) preg_replace( '/(^|>)[^<]+/', '$1', $closes );

			return '<' . $level . $anchor_attrs . '>' . $kept_id . $opens . $inner . $closes . '</' . $level . '>';
		},
		$content
	);

	// null on invalid UTF-8 or a PCRE limit: leave the content as it was.
	return $unwrapped ?? $content;
}
