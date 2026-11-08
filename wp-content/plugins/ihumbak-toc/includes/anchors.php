<?php
/**
 * Kotwice of new headings and the Spis treści of the block and [ihumbak_anchor_toc].
 *
 * Every h2–h3 without an id gets one: sanitize_title() of its text (Polish letters
 * transliterated), "-2", "-3" … when the id is already taken on the page, "naglowek" when
 * the text gives nothing. Ids already in the content (v1 Kotwice, ids typed by an editor,
 * ids of v2 posts) never change.
 *
 * Kotwice stay put because they are written into the post: on save every core/heading h2–h3
 * without an id gets one in post_content (core/heading keeps it as its "anchor" attribute).
 * Headings that are not core/heading blocks (classic posts, HTML blocks, dynamic blocks) get
 * the same ids at render time.
 *
 * A heading carrying SKIP_ATTRIBUTE (data-toc-skip), or inside an element carrying it, is
 * left alone: no Kotwica, not in the Spis treści, not in the rail (e.g. the tax calculator's "Wynik").
 * The attribute counts only on the heading itself or on an element of SKIP_CONTAINERS (non-void,
 * end tag required) that is not written self-closing and has its matching end tag in the
 * fragment; anywhere else (p, li, svg, an unclosed div …) it is ignored, so an implicitly
 * closed element can never swallow the rest of the page. The v1 pass (legacy-anchors.php)
 * honours it only on the Kotwica heading itself.
 *
 * The Spis treści is built from the rendered content: the block and the shortcode leave a
 * placeholder, and a the_content pass after the v1 one (priority 25) gives headings their ids
 * and swaps each placeholder for the list.
 */

namespace Ihumbak\Toc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PLACEHOLDER_PREFIX = '<!-- ihumbak-toc:toc ';
const PLACEHOLDER_SUFFIX = ' -->';
const FALLBACK_ID        = 'naglowek';
const SKIP_ATTRIBUTE     = 'data-toc-skip';
// Elements whose SKIP_ATTRIBUTE excludes the headings inside them: their end tag is required,
// so the region ends exactly at the matching closer.
const SKIP_CONTAINERS = array( 'DIV', 'SECTION', 'ASIDE', 'FIGURE', 'FORM', 'ARTICLE', 'NAV' );

add_shortcode( 'ihumbak_anchor_toc', __NAMESPACE__ . '\render_shortcode' );
add_filter( 'the_content', __NAMESPACE__ . '\finish_content', 30 );
add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\persist_anchors', 10, 2 );

/**
 * [ihumbak_anchor_toc] (v2, 2.0 read a post_id attribute; the list is now always the current post's).
 */
function render_shortcode(): string {
	return toc_for_post( (int) get_the_ID() );
}

/**
 * Spis treści of a post, for the block and the shortcode.
 *
 * Inside that post's the_content it returns a placeholder (the headings and their ids are
 * known only once the content is rendered); anywhere else, e.g. in the Kolumna boczna, the
 * finished list.
 *
 * @param int    $post_id            Post whose headings are listed.
 * @param string $wrapper_attributes Extra attributes for the <nav>, e.g. get_block_wrapper_attributes().
 * @param bool   $unless_in_content  Outside the content only: print nothing when the post's content
 *                                   already shows a Spis treści (its own block, shortcode or v1 menu),
 *                                   so a template-placed list never doubles it.
 */
function toc_for_post( int $post_id, string $wrapper_attributes = '', bool $unless_in_content = false ): string {
	if ( ! $post_id || is_toc_hidden( $post_id ) ) {
		return '';
	}

	if ( doing_filter( 'the_content' ) && get_the_ID() === $post_id ) {
		return PLACEHOLDER_PREFIX . base64_encode( $wrapper_attributes ) . PLACEHOLDER_SUFFIX;
	}

	if ( $unless_in_content && content_shows_toc( $post_id ) ) {
		return '';
	}

	return add_wrapper_attributes( render_toc( headings_for_post( $post_id ) ), $wrapper_attributes );
}

/**
 * h2–h3 of a post's rendered content, in document order, as render_toc() takes them.
 *
 * Uses the list remembered while the post's the_content ran; otherwise renders the content
 * (once per request and post). A Spis treści inside that content renders nothing then.
 *
 * @return array<int, array{level: int, id: string, text: string}>
 */
function headings_for_post( int $post_id ): array {
	static $rendering = array();

	$known = remembered_headings( $post_id );
	if ( null !== $known ) {
		return $known;
	}
	if ( isset( $rendering[ $post_id ] ) ) {
		return array();
	}

	$post = get_post( $post_id );
	if ( ! $post || post_password_required( $post ) ) {
		return array();
	}

	$rendering[ $post_id ] = true;
	$previous              = $GLOBALS['post'] ?? null;
	$GLOBALS['post']       = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	setup_postdata( $post );
	// The HTML of this pass is thrown away, so its images must not use up core's fetchpriority="high"
	// hint and eager-loading slots: the real render would get only lazy images (#81).
	add_filter( 'pre_wp_get_loading_optimization_attributes', '__return_empty_array' );
	apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	remove_filter( 'pre_wp_get_loading_optimization_attributes', '__return_empty_array' );
	$GLOBALS['post'] = $previous; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	if ( $previous instanceof \WP_Post ) {
		setup_postdata( $previous );
	}
	unset( $rendering[ $post_id ] );

	return remembered_headings( $post_id ) ?? array();
}

/**
 * Whether the post's rendered content contains a Spis treści of its own.
 * Renders the content once when it has not run yet in this request (see headings_for_post()).
 */
function content_shows_toc( int $post_id ): bool {
	headings_for_post( $post_id );

	return (bool) remembered_toc_in_content( $post_id );
}

/**
 * Whether a post's the_content in this request printed a Spis treści.
 *
 * @param bool|null $shows To remember; null reads.
 */
function remembered_toc_in_content( int $post_id, ?bool $shows = null ): ?bool {
	static $seen = array();

	if ( null !== $shows ) {
		$seen[ $post_id ] = $shows;
	}

	return $seen[ $post_id ] ?? null;
}

/**
 * Headings seen in a post's the_content during this request.
 *
 * @param array<int, array{level: int, id: string, text: string}>|null $headings To remember; null reads.
 * @return array<int, array{level: int, id: string, text: string}>|null
 */
function remembered_headings( int $post_id, ?array $headings = null ): ?array {
	static $seen = array();

	if ( null !== $headings ) {
		$seen[ $post_id ] = $headings;
	}

	return $seen[ $post_id ] ?? null;
}

/**
 * the_content, after the v1 pass: ids for h2–h3 without one, then the Spis treści placeholders.
 */
function finish_content( $content ) {
	if ( ! is_string( $content ) || '' === $content ) {
		return $content;
	}

	$headings = array();
	$content  = add_heading_ids( $content, $headings );

	$post_id = (int) get_the_ID();
	if ( $post_id ) {
		remembered_headings( $post_id, $headings );
		// Placeholders of the block / shortcode, or the v1 menu already printed at priority 25.
		remembered_toc_in_content( $post_id, str_contains( $content, PLACEHOLDER_PREFIX ) || str_contains( $content, '<nav class="ihumbak-toc' ) );
	}

	if ( ! str_contains( $content, PLACEHOLDER_PREFIX ) ) {
		return $content;
	}

	$toc = render_toc( $headings );

	// A shortcode alone in a paragraph must not leave <p><nav></nav></p> behind.
	$pattern = '#(?:<p>\s*)?' . preg_quote( PLACEHOLDER_PREFIX, '#' ) . '([A-Za-z0-9+/=]*)' . preg_quote( PLACEHOLDER_SUFFIX, '#' ) . '(?:\s*</p>)?#';

	return (string) preg_replace_callback(
		$pattern,
		static function ( array $m ) use ( $toc ): string {
			$open  = str_starts_with( $m[0], '<p>' );
			$close = str_ends_with( $m[0], '</p>' );
			$html  = add_wrapper_attributes( $toc, (string) base64_decode( $m[1], true ) );
			// Keep the paragraph when only one side of it was matched.
			return ( $open && ! $close ? '<p>' : '' ) . $html . ( $close && ! $open ? '</p>' : '' );
		},
		$content
	);
}

/**
 * Merges attributes (as printed by get_block_wrapper_attributes()) into the Spis treści <nav>.
 */
function add_wrapper_attributes( string $toc, string $attributes ): string {
	if ( '' === $toc || '' === trim( $attributes ) ) {
		return $toc;
	}

	$source = new \WP_HTML_Tag_Processor( '<div ' . $attributes . '>' );
	$nav    = new \WP_HTML_Tag_Processor( $toc );
	if ( ! $source->next_tag() || ! $nav->next_tag( 'nav' ) ) {
		return $toc;
	}

	foreach ( (array) $source->get_attribute_names_with_prefix( '' ) as $name ) {
		$value = $source->get_attribute( $name );
		if ( 'class' === $name ) {
			foreach ( preg_split( '/\s+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY ) as $class ) {
				$nav->add_class( $class );
			}
		} elseif ( 'aria-label' !== $name ) {
			$nav->set_attribute( $name, $value );
		}
	}

	return $nav->get_updated_html();
}

/**
 * h2 and h3 of an HTML fragment, in document order.
 *
 * @return array{headings: array<int, array{level: int, id: string, text: string, skip: bool, excluded: bool, block: bool}>, ids: array<string, true>}
 *         "skip": a v1 Kotwica (left to the v1 pass); "excluded": under SKIP_ATTRIBUTE;
 *         "block": the first tag of a core/heading block.
 */
function scan_headings( string $html ): array {
	$headings = array();
	$ids      = array();
	$current  = null;
	$after_heading_comment = false;
	// Tag tokens (by ordinal) that open a skip region: SKIP_CONTAINERS with a matching closer.
	$skip_openers = skip_openers( $html );
	// The element carrying SKIP_ATTRIBUTE that is open, and how deep its tag is nested in itself.
	$skip_tag   = null;
	$skip_depth = 0;
	$tag_index  = -1;

	$p = new \WP_HTML_Tag_Processor( $html );
	while ( $p->next_token() ) {
		$type = $p->get_token_type();

		if ( '#tag' === $type ) {
			$tag = $p->get_tag();
			++$tag_index;
			if ( ! $p->is_tag_closer() ) {
				$id = $p->get_attribute( 'id' );
				if ( is_string( $id ) && '' !== $id ) {
					$ids[ $id ] = true;
				}
			}
			if ( null !== $skip_tag ) {
				if ( $tag === $skip_tag ) {
					$skip_depth += $p->is_tag_closer() ? -1 : 1;
				}
			} elseif ( isset( $skip_openers[ $tag_index ] ) ) {
				$skip_tag   = $tag;
				$skip_depth = 1;
			}
			if ( 'H2' === $tag || 'H3' === $tag ) {
				if ( ! $p->is_tag_closer() && null === $current ) {
					$id      = $p->get_attribute( 'id' );
					$current = array(
						'tag'   => $tag,
						'level' => 'H2' === $tag ? 2 : 3,
						'id'    => is_string( $id ) ? $id : '',
						'text'  => '',
						'skip'     => (bool) $p->has_class( V1\ANCHOR_CLASS ),
						'excluded' => null !== $skip_tag || null !== $p->get_attribute( SKIP_ATTRIBUTE ),
						'block'    => $after_heading_comment,
					);
				} elseif ( $p->is_tag_closer() && null !== $current && $tag === $current['tag'] ) {
					$current['text'] = trim( (string) preg_replace( '/\s+/u', ' ', $current['text'] ) );
					unset( $current['tag'] );
					$headings[] = $current;
					$current    = null;
				}
			}
			if ( null !== $skip_tag && $skip_depth < 1 ) {
				$skip_tag = null;
			}
			$after_heading_comment = false;
		} elseif ( '#text' === $type ) {
			if ( null !== $current ) {
				$current['text'] .= $p->get_modifiable_text();
			}
			if ( '' !== trim( $p->get_modifiable_text() ) ) {
				$after_heading_comment = false;
			}
		} elseif ( '#comment' === $type ) {
			$after_heading_comment = (bool) preg_match( '#^\s*wp:heading(\s|$)#', $p->get_modifiable_text() );
		} else {
			$after_heading_comment = false;
		}
	}

	return array(
		'headings' => $headings,
		'ids'      => $ids,
	);
}

/**
 * Ordinals of the tag tokens of $html that open a SKIP_ATTRIBUTE region (counting every tag
 * token, openers and closers, from 0).
 *
 * Only an element of SKIP_CONTAINERS, not written self-closing, whose matching end tag is in
 * the fragment: an element left unclosed is treated as if it had no attribute, so it never
 * hides the headings after it.
 *
 * @return array<int, true>
 */
function skip_openers( string $html ): array {
	if ( false === stripos( $html, SKIP_ATTRIBUTE ) ) {
		return array();
	}

	$openers = array();
	$open    = array_fill_keys( SKIP_CONTAINERS, array() ); // Per tag: ordinals of open elements.
	$index   = -1;

	$p = new \WP_HTML_Tag_Processor( $html );
	while ( $p->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
		++$index;
		$tag = $p->get_tag();
		if ( ! isset( $open[ $tag ] ) ) {
			continue;
		}
		if ( ! $p->is_tag_closer() ) {
			$open[ $tag ][] = $index;
			if ( null !== $p->get_attribute( SKIP_ATTRIBUTE ) && ! $p->has_self_closing_flag() ) {
				$openers[ $index ] = false;
			}
		} elseif ( array() !== $open[ $tag ] ) {
			$opener = array_pop( $open[ $tag ] );
			if ( isset( $openers[ $opener ] ) ) {
				$openers[ $opener ] = true;
			}
		}
	}

	return array_fill_keys( array_keys( array_filter( $openers ) ), true );
}

/**
 * Kotwica for a heading text: sanitize_title(), unique among $used ("-2", "-3" …).
 *
 * @param array<string, true> $used Ids taken on the page; the new one is added.
 */
function new_anchor( string $text, array &$used ): string {
	$base = sanitize_title( $text );
	if ( '' === $base ) {
		$base = FALLBACK_ID;
	}

	$id = $base;
	for ( $n = 2; isset( $used[ $id ] ); $n++ ) {
		$id = $base . '-' . $n;
	}
	$used[ $id ] = true;

	return $id;
}

/**
 * Gives every h2–h3 without an id its Kotwica.
 *
 * @param string   $html       Content.
 * @param array    $headings   Filled with the h2–h3 for render_toc().
 * @param bool     $only_block Only write ids on core/heading blocks (raw post_content on save).
 */
function add_heading_ids( string $html, array &$headings = array(), bool $only_block = false ): string {
	$scan = scan_headings( $html );
	$used = $scan['ids'];

	$new      = array();
	$headings = array();
	foreach ( $scan['headings'] as $i => $heading ) {
		if ( $heading['excluded'] ) {
			continue;
		}
		if ( '' === $heading['id'] && ! $heading['skip'] ) {
			// Raw content: a heading still holding a shortcode gets its text only at render.
			if ( ! $only_block || ( $heading['block'] && ! str_contains( $heading['text'], '[' ) ) ) {
				$heading['id'] = new_anchor( $heading['text'], $used );
				$new[ $i ]     = $heading['id'];
			}
		}
		$headings[] = array(
			'level' => $heading['level'],
			'id'    => $heading['id'],
			'text'  => $heading['text'],
		);
	}

	if ( ! $new ) {
		return $html;
	}

	$p = new \WP_HTML_Tag_Processor( $html );
	$i = -1;
	$open = false;
	while ( $p->next_tag( array( 'tag_closers' => 'visit' ) ) ) {
		$tag = $p->get_tag();
		if ( 'H2' !== $tag && 'H3' !== $tag ) {
			continue;
		}
		// Count headings the way scan_headings() does: an h2/h3 opened inside another one is part of it.
		if ( $p->is_tag_closer() ) {
			if ( $open === $tag ) {
				$open = false;
			}
			continue;
		}
		if ( false !== $open ) {
			continue;
		}
		$open = $tag;
		++$i;
		if ( isset( $new[ $i ] ) ) {
			$p->set_attribute( 'id', $new[ $i ] );
		}
	}

	return $p->get_updated_html();
}

/**
 * wp_insert_post_data: writes the Kotwice of new core/heading h2–h3 into post_content,
 * so they no longer depend on the heading text.
 *
 * @param array $data    Slashed post data.
 * @param array $postarr Raw post data.
 */
function persist_anchors( $data, $postarr = array() ) {
	if ( ! is_array( $data ) || empty( $data['post_content'] ) || empty( $data['post_type'] ) ) {
		return $data;
	}
	if ( 'revision' === $data['post_type'] || ! post_type_supports( $data['post_type'], 'editor' ) || ! is_post_type_viewable( $data['post_type'] ) ) {
		return $data;
	}

	$content = wp_unslash( $data['post_content'] );
	if ( ! has_blocks( $content ) || ! str_contains( $content, 'wp:heading' ) ) {
		return $data;
	}

	$headings = array();
	$updated  = add_heading_ids( $content, $headings, true );
	if ( $updated !== $content ) {
		$data['post_content'] = wp_slash( $updated );
	}

	return $data;
}
