/**
 * Editor of the header blocks — no build, no JSX (spec #36 toolchain).
 * All three blocks are dynamic: save() returns null or InnerBlocks.Content,
 * the wrappers are rendered in PHP.
 */
( function ( blocks, blockEditor, element, i18n ) {
	const el = element.createElement;
	const { __ } = i18n;
	const { useBlockProps, useInnerBlocksProps, InnerBlocks } = blockEditor;

	const searchIcon = el(
		'svg',
		{
			viewBox: '0 0 24 24',
			width: 22,
			height: 22,
			fill: 'none',
			stroke: 'currentColor',
			strokeWidth: 2,
			strokeLinecap: 'round',
			'aria-hidden': true,
		},
		el( 'circle', { cx: 11, cy: 11, r: 7 } ),
		el( 'path', { d: 'M20 20l-3.5-3.5' } )
	);

	blocks.registerBlockType( 'ihumbak/site-header', {
		icon: 'table-row-before',
		edit() {
			return el( 'div', useInnerBlocksProps( useBlockProps( { className: 'sb-header' } ) ) );
		},
		save() {
			return el( InnerBlocks.Content );
		},
	} );

	blocks.registerBlockType( 'ihumbak/search-toggle', {
		icon: 'search',
		edit() {
			return el(
				'div',
				useBlockProps(),
				el(
					'span',
					{ className: 'sb-icon-button', 'aria-label': __( 'Szukaj', 'ihumbak-site-blocks' ) },
					searchIcon
				)
			);
		},
		save() {
			return null;
		},
	} );

	blocks.registerBlockType( 'ihumbak/search-bar', {
		icon: 'search',
		edit() {
			const inner = useInnerBlocksProps(
				{ className: 'wp-block-ihumbak-search-bar__inner' },
				{
					template: [
						[
							'core/search',
							{
								label: __( 'Szukaj na blogu', 'ihumbak-site-blocks' ),
								showLabel: false,
								placeholder: __( 'Szukaj na blogu…', 'ihumbak-site-blocks' ),
								buttonText: __( 'Szukaj', 'ihumbak-site-blocks' ),
								className: 'sb-search-bar__form',
							},
						],
						[
							'core/group',
							{ className: 'sb-search-bar__hints', layout: { type: 'flex', flexWrap: 'wrap' } },
							[
								[ 'core/paragraph', { content: __( 'Popularne:', 'ihumbak-site-blocks' ) } ],
								[ 'core/buttons', { className: 'is-style-badges' } ],
							],
						],
					],
				}
			);
			return el( 'div', useBlockProps(), el( 'div', inner ) );
		},
		save() {
			return el( InnerBlocks.Content );
		},
	} );

	/*
	 * Query variation „Z tej kategorii”: the server (includes/same-category-query.php)
	 * narrows it to the current post's categories and drops the current post.
	 * Without a current post (template editor) the preview lists the newest posts.
	 */
	blocks.registerBlockVariation( 'core/query', {
		name: 'ihumbak/same-category',
		title: __( 'Z tej kategorii', 'ihumbak-site-blocks' ),
		description: __(
			'Wpisy z kategorii bieżącego wpisu, bez niego. Ukryte, gdy nie ma innych wpisów.',
			'ihumbak-site-blocks'
		),
		icon: 'category',
		scope: [ 'inserter' ],
		isActive: [ 'namespace' ],
		allowedControls: [ 'order' ],
		attributes: {
			namespace: 'ihumbak/same-category',
			tagName: 'section',
			query: {
				perPage: 3,
				pages: 0,
				offset: 0,
				postType: 'post',
				order: 'desc',
				orderBy: 'date',
				inherit: false,
				sticky: 'ignore',
			},
		},
		innerBlocks: [
			[ 'core/heading', { content: __( 'Z tej kategorii', 'ihumbak-site-blocks' ) } ],
			[
				'core/post-template',
				{},
				[
					[ 'core/post-featured-image', { aspectRatio: '16/10' } ],
					[ 'core/post-title', { level: 3, isLink: true } ],
					[ 'core/post-date' ],
				],
			],
		],
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element, window.wp.i18n );
