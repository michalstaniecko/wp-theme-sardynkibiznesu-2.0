/**
 * Block editor: "Spis treści" panel in the post/page settings with the "Ukryj spis treści"
 * checkbox (post meta _ihumbak_toc_hidden, includes/hide.php). No build (spec #36 toolchain).
 */
( function ( plugins, editor, element, components, data, coreData ) {
	const el = element.createElement;
	const META_KEY = '_ihumbak_toc_hidden';
	const POST_TYPES = [ 'post', 'page' ];

	const HidePanel = () => {
		const postType = data.useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );
		const [ meta, setMeta ] = coreData.useEntityProp( 'postType', postType, 'meta' );

		if ( ! POST_TYPES.includes( postType ) || ! meta ) {
			return null;
		}

		return el(
			editor.PluginDocumentSettingPanel,
			{ name: 'ihumbak-toc-hide', title: 'Spis treści' },
			el( components.CheckboxControl, {
				__nextHasNoMarginBottom: true,
				label: 'Ukryj spis treści',
				help: 'Spis treści nie pojawi się w kolumnie bocznej ani w treści. Kotwice nagłówków zostają.',
				checked: !! meta[ META_KEY ],
				onChange: ( value ) => setMeta( { ...meta, [ META_KEY ]: value } ),
			} )
		);
	};

	plugins.registerPlugin( 'ihumbak-toc-hide', { render: HidePanel } );
} )( window.wp.plugins, window.wp.editor, window.wp.element, window.wp.components, window.wp.data, window.wp.coreData );
