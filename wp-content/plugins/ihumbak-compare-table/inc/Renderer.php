<?php
/**
 * Server-side rendering of the ihumbak/compare-table block tree.
 *
 * All frontend markup is produced here and in templates/, from the parsed
 * block tree. Nothing is read from saved HTML, so a template change shows up
 * on existing posts without re-saving them.
 */

namespace Ihumbak\CompareTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Renderer {

	/**
	 * The narrow layout (N equal columns identified by the sticky header) is
	 * only valid up to this many service columns, so anything beyond that is
	 * dropped. Mirrors MAX_COLUMNS in src/utils/columns.js.
	 */
	const MAX_COLUMNS = 4;

	const BLOCK_TABLE   = 'ihumbak/compare-table';
	const BLOCK_HEADER  = 'ihumbak/compare-header';
	const BLOCK_COLUMN  = 'ihumbak/compare-column';
	const BLOCK_SECTION = 'ihumbak/compare-section';
	const BLOCK_ROW     = 'ihumbak/compare-row';
	const BLOCK_VALUE   = 'ihumbak/compare-value';

	/**
	 * Attribute defaults. They mirror block.json: an attribute equal to its
	 * default is not written to the block comment, so PHP has to restore it.
	 */
	const DEFAULTS = array(
		self::BLOCK_TABLE   => array(
			'caption'            => '',
			'featureColumnLabel' => '',
			'stickyHeader'       => true,
			'headingLevel'       => 3,
		),
		self::BLOCK_COLUMN  => array(
			'name'         => '',
			'price'        => '',
			'priceNote'    => '',
			'imageId'      => 0,
			'imageAlt'     => '',
			'buttonText'   => '',
			'buttonUrl'    => '',
			'buttonNewTab' => false,
			'buttonRel'    => '',
		),
		self::BLOCK_SECTION => array(
			'title'       => '',
			'defaultOpen' => true,
		),
		self::BLOCK_ROW     => array(
			'title'       => '',
			'description' => '',
		),
		self::BLOCK_VALUE   => array(
			'icon'    => 'none',
			'text'    => '',
			'tooltip' => '',
		),
	);

	/**
	 * Tags allowed in text printed inside interactive or inline elements (a
	 * button, a link, a tooltip): formatting only, no attributes, nothing
	 * interactive or block-level.
	 */
	const INLINE_TAGS = array(
		'strong' => array(),
		'em'     => array(),
		'b'      => array(),
		'i'      => array(),
		'br'     => array(),
		'span'   => array(),
		'sup'    => array(),
		'sub'    => array(),
	);

	/**
	 * Render callback of ihumbak/compare-table.
	 *
	 * $content is ignored on purpose: the child blocks save no markup.
	 *
	 * @param array     $attributes Block attributes, defaults from block.json applied.
	 * @param string    $content    Rendered inner blocks (unused).
	 * @param \WP_Block $block      Block instance.
	 */
	public static function render( array $attributes, string $content, \WP_Block $block ): string {
		$parsed = is_array( $block->parsed_block ) ? $block->parsed_block : array();

		$parsed['attrs'] = array_merge(
			isset( $parsed['attrs'] ) && is_array( $parsed['attrs'] ) ? $parsed['attrs'] : array(),
			$attributes
		);

		return self::render_parsed( $parsed, true );
	}

	/**
	 * Renders a parsed ihumbak/compare-table block (one item of parse_blocks()).
	 *
	 * @param array $parsed_block      Parsed block array: attrs + innerBlocks.
	 * @param bool  $use_block_wrapper Take the root attributes from get_block_wrapper_attributes().
	 *                                 Only valid while WordPress is rendering the block.
	 */
	public static function render_parsed( array $parsed_block, bool $use_block_wrapper = false ): string {
		$model = self::build_model( $parsed_block );

		if ( null === $model ) {
			return '';
		}

		wp_enqueue_style( 'ihumbak-compare-table' );

		foreach ( $model['sections'] as $section ) {
			if ( $section['collapsible'] ) {
				self::arm_noscript_style();
				break;
			}
		}

		$root = array(
			'class' => 'ihumbak-ct ihumbak-ct--cols-' . $model['cols'] . ( $model['sticky'] ? ' ihumbak-ct--sticky' : '' ),
			'style' => '--ct-cols:' . $model['cols'] . ';',
		);

		if ( $use_block_wrapper && function_exists( 'get_block_wrapper_attributes' ) ) {
			$model['wrapper_attributes'] = get_block_wrapper_attributes( $root );
		} else {
			$model['wrapper_attributes'] = sprintf(
				'class="%s" style="%s"',
				esc_attr( $root['class'] ),
				esc_attr( $root['style'] )
			);
		}

		return self::template( 'table', $model );
	}

	/**
	 * Includes templates/<name>.php with $args in scope and returns its output.
	 */
	public static function template( string $name, array $args = array() ): string {
		$file = dirname( __DIR__ ) . '/templates/' . $name . '.php';

		if ( ! is_readable( $file ) ) {
			return '';
		}

		$include = static function ( string $__file, array $args ) {
			include $__file;
		};

		ob_start();
		$include( $file, $args );

		return (string) ob_get_clean();
	}

	/**
	 * Escapes RichText printed inside an interactive or inline element.
	 *
	 * @param mixed $html Attribute value.
	 */
	public static function kses_inline( $html ): string {
		return is_scalar( $html ) ? wp_kses( (string) $html, self::INLINE_TAGS ) : '';
	}

	/**
	 * Prints the no-JS fallback once per page, however many tables it has.
	 *
	 * Kept out of the table markup, where the CSS text would end up in
	 * excerpts and other strip_tags() consumers of the rendered content.
	 */
	public static function arm_noscript_style(): void {
		$callback = array( self::class, 'print_noscript_style' );

		if ( ! has_action( 'wp_footer', $callback ) ) {
			add_action( 'wp_footer', $callback );
		}
	}

	/**
	 * Without JavaScript a section toggle does nothing, so every section is
	 * forced open and the toggles lose their affordance.
	 */
	public static function print_noscript_style(): void {
		echo '<noscript><style>'
			. '.ihumbak-ct .ihumbak-ct__section-body{grid-template-rows:1fr!important;transition:none!important}'
			. '.ihumbak-ct .ihumbak-ct__section-body-inner{overflow:visible!important;visibility:visible!important;transition:none!important}'
			. '.ihumbak-ct .ihumbak-ct__section-toggle{cursor:default;pointer-events:none}'
			. '.ihumbak-ct .ihumbak-ct__section-toggle-icon{display:none}'
			. '</style></noscript>' . "\n";
	}

	/**
	 * Whether a RichText value holds anything visible.
	 *
	 * @param mixed $value Attribute value.
	 */
	public static function has_text( $value ): bool {
		if ( ! is_scalar( $value ) ) {
			return false;
		}

		$text = wp_strip_all_tags( (string) $value );
		$text = str_replace( array( '&nbsp;', "\xC2\xA0" ), ' ', $text );

		return '' !== trim( $text );
	}

	/**
	 * Decorative inline SVG. Always aria-hidden: the text alternative is
	 * rendered next to it by the template.
	 */
	public static function icon( string $name ): string {
		$paths = array(
			'check'   => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
			'minus'   => '<path d="M6 12h12"/>',
			'info'    => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5"/><path d="M12 7.5v.01"/>',
			'chevron' => '<path d="M6 9l6 6 6-6"/>',
		);

		if ( ! isset( $paths[ $name ] ) ) {
			return '';
		}

		return sprintf(
			'<svg class="ihumbak-ct__icon ihumbak-ct__icon--%1$s" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
			esc_attr( $name ),
			$paths[ $name ]
		);
	}

	/**
	 * Turns the parsed block tree into the data the templates need.
	 *
	 * @return array|null Null when there is nothing to render (no service columns).
	 */
	private static function build_model( array $parsed_block ): ?array {
		$attrs    = self::attrs( $parsed_block, self::BLOCK_TABLE );
		$children = self::children( $parsed_block );
		$columns  = array();

		// The header's columns are the source of truth for the column count.
		foreach ( $children as $child ) {
			if ( self::BLOCK_HEADER !== $child['blockName'] ) {
				continue;
			}

			foreach ( self::children( $child, self::BLOCK_COLUMN ) as $column ) {
				$columns[] = self::build_column( $column );
			}

			break;
		}

		$columns = array_slice( $columns, 0, self::MAX_COLUMNS );
		$cols    = count( $columns );

		if ( 0 === $cols ) {
			return null;
		}

		$uid      = wp_unique_id( 'ihumbak-ct-' );
		$sections = array();
		$tooltips = 0;

		foreach ( $children as $child ) {
			if ( self::BLOCK_SECTION !== $child['blockName'] ) {
				continue;
			}

			$section = self::build_section( $child, $cols, $uid, count( $sections ), $tooltips );

			if ( null !== $section ) {
				$sections[] = $section;
			}
		}

		$buttons = array();

		foreach ( $columns as $column ) {
			$buttons[] = $column['button'];
		}

		return array(
			'uid'           => $uid,
			'cols'          => $cols,
			'sticky'        => $attrs['stickyHeader'],
			'heading_level' => min( 6, max( 2, $attrs['headingLevel'] ) ),
			'caption'       => self::has_text( $attrs['caption'] ) ? (string) $attrs['caption'] : '',
			'feature_label' => self::has_text( $attrs['featureColumnLabel'] ) ? (string) $attrs['featureColumnLabel'] : '',
			'columns'       => $columns,
			'sections'      => $sections,
			'buttons'       => $buttons,
			'has_buttons'   => (bool) array_filter( $buttons ),
		);
	}

	private static function build_column( array $block ): array {
		$attrs = self::attrs( $block, self::BLOCK_COLUMN );
		$name  = self::has_text( $attrs['name'] ) ? (string) $attrs['name'] : '';

		$button = null;
		$url    = esc_url( trim( $attrs['buttonUrl'] ) );

		if ( '' !== $url ) {
			$new_tab = $attrs['buttonNewTab'];
			$rel     = preg_split( '/[\s,]+/', strtolower( $attrs['buttonRel'] ), -1, PREG_SPLIT_NO_EMPTY );
			$rel     = array_filter( array_map( 'sanitize_html_class', $rel ) );

			if ( $new_tab ) {
				$rel[] = 'noopener';
			}

			$button = array(
				// Already passed through esc_url().
				'url'     => $url,
				'text'    => self::has_text( $attrs['buttonText'] )
					? (string) $attrs['buttonText']
					: __( 'Zobacz ofertę', 'ihumbak-compare-table' ),
				'new_tab' => $new_tab,
				'rel'     => implode( ' ', array_unique( $rel ) ),
				// Plain text, used to tell identical buttons apart for screen readers.
				'service' => trim( wp_strip_all_tags( $name ) ),
			);
		}

		return array(
			'name'       => $name,
			'price'      => self::has_text( $attrs['price'] ) ? (string) $attrs['price'] : '',
			'price_note' => self::has_text( $attrs['priceNote'] ) ? (string) $attrs['priceNote'] : '',
			'image_id'   => max( 0, $attrs['imageId'] ),
			'image_alt'  => trim( $attrs['imageAlt'] ),
			'button'     => $button,
		);
	}

	/**
	 * @param int $tooltips Running tooltip counter of the table, for unique IDs.
	 *
	 * @return array|null Null when the section has no rows to show.
	 */
	private static function build_section( array $block, int $cols, string $uid, int $index, int &$tooltips ): ?array {
		$attrs = self::attrs( $block, self::BLOCK_SECTION );
		$rows  = array();

		foreach ( self::children( $block, self::BLOCK_ROW ) as $row_block ) {
			$row = self::build_row( $row_block, $cols, $uid, $tooltips );

			if ( null !== $row ) {
				$rows[] = $row;
			}
		}

		if ( ! $rows ) {
			return null;
		}

		$title       = self::has_text( $attrs['title'] ) ? (string) $attrs['title'] : '';
		$collapsible = '' !== $title;

		return array(
			'title'       => $title,
			// Without a title there is no toggle, so the section can never be closed.
			'collapsible' => $collapsible,
			'open'        => ! $collapsible || $attrs['defaultOpen'],
			'body_id'     => $uid . '-section-' . ( $index + 1 ),
			'rows'        => $rows,
		);
	}

	/**
	 * @return array|null Null for a row with no content at all (an untouched placeholder).
	 */
	private static function build_row( array $block, int $cols, string $uid, int &$tooltips ): ?array {
		$attrs       = self::attrs( $block, self::BLOCK_ROW );
		$title       = self::has_text( $attrs['title'] ) ? (string) $attrs['title'] : '';
		$description = self::has_text( $attrs['description'] ) ? (string) $attrs['description'] : '';
		$values      = array();
		$has_value   = false;

		// Columns are matched by position: extra cells are dropped, missing ones padded.
		$cells = array_slice( self::children( $block, self::BLOCK_VALUE ), 0, $cols );

		for ( $i = 0; $i < $cols; $i++ ) {
			$cell_attrs = self::attrs( isset( $cells[ $i ] ) ? $cells[ $i ] : array(), self::BLOCK_VALUE );
			$icon       = in_array( $cell_attrs['icon'], array( 'check', 'minus' ), true ) ? $cell_attrs['icon'] : 'none';
			$text       = self::has_text( $cell_attrs['text'] ) ? (string) $cell_attrs['text'] : '';
			$tooltip    = self::has_text( $cell_attrs['tooltip'] ) ? (string) $cell_attrs['tooltip'] : '';
			$tooltip_id = '';

			if ( '' !== $tooltip ) {
				$tooltip_id = $uid . '-tip-' . ( ++$tooltips );
			}

			if ( 'none' !== $icon || '' !== $text || '' !== $tooltip ) {
				$has_value = true;
			}

			$values[] = array(
				'icon'       => $icon,
				'text'       => $text,
				'tooltip'    => $tooltip,
				'tooltip_id' => $tooltip_id,
			);
		}

		if ( '' === $title && '' === $description && ! $has_value ) {
			return null;
		}

		return array(
			'title'       => $title,
			'description' => $description,
			'values'      => $values,
		);
	}

	/**
	 * Attributes of a parsed block with the defaults filled in.
	 *
	 * Inner blocks are read straight from the block comment, which WordPress
	 * does not validate against block.json. Every value is therefore brought
	 * to the type of its default here; one of the wrong kind (an array where
	 * a string is expected, a non-numeric ID) falls back to the default.
	 */
	private static function attrs( array $block, string $block_name ): array {
		$raw   = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		$attrs = array();

		foreach ( self::DEFAULTS[ $block_name ] as $key => $default ) {
			$value = array_key_exists( $key, $raw ) ? $raw[ $key ] : $default;

			if ( is_bool( $default ) ) {
				$value = is_scalar( $value ) ? (bool) $value : $default;
			} elseif ( is_int( $default ) ) {
				$value = is_numeric( $value ) ? (int) $value : $default;
			} else {
				$value = is_scalar( $value ) ? (string) $value : $default;
			}

			$attrs[ $key ] = $value;
		}

		return $attrs;
	}

	/**
	 * Named inner blocks of a parsed block (skips the whitespace-only freeform blocks).
	 */
	private static function children( array $block, string $only = '' ): array {
		$children = array();

		if ( empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
			return $children;
		}

		foreach ( $block['innerBlocks'] as $child ) {
			if ( ! is_array( $child ) || empty( $child['blockName'] ) ) {
				continue;
			}

			if ( '' !== $only && $only !== $child['blockName'] ) {
				continue;
			}

			$children[] = $child;
		}

		return $children;
	}
}
