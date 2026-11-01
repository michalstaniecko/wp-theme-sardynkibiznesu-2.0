/**
 * „Obrazek na stronie” panel in the page sidebar: whether the page displays its Obrazek wyróżniający
 * (post meta registered in includes/page-featured-image.php). No build: wp.element.createElement, no JSX.
 */
( function ( wp ) {
	const el = wp.element.createElement;
	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel, store: editorStore } = wp.editor;
	const { ToggleControl } = wp.components;
	const { useSelect } = wp.data;
	const { useEntityProp } = wp.coreData;

	const META_SHOW = '_sb_page_show_featured_image';

	function PageFeaturedImagePanel() {
		const postType = useSelect( ( select ) => select( editorStore ).getCurrentPostType(), [] );
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

		if ( 'page' !== postType || ! meta ) {
			return null;
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'ihumbak-page-featured-image',
				title: 'Obrazek na stronie',
				className: 'ihumbak-page-featured-image-panel',
			},
			el( ToggleControl, {
				__nextHasNoMarginBottom: true,
				label: 'Pokazuj obrazek wyróżniający na stronie',
				help: 'Wyłączony obrazek nadal działa na kartach i przy udostępnianiu. Szablon „Strona szeroka” nie pokazuje obrazka. Podgląd szablonu w edytorze może pokazywać obrazek także po wyłączeniu.',
				checked: !! meta[ META_SHOW ],
				onChange: ( value ) => setMeta( { ...meta, [ META_SHOW ]: value } ),
			} )
		);
	}

	registerPlugin( 'ihumbak-page-featured-image', { render: PageFeaturedImagePanel } );
} )( window.wp );
