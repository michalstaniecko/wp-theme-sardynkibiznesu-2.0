/**
 * "Podcast" panel in the post sidebar: episode number and duration (post meta
 * registered in includes/podcast.php). No build: wp.element.createElement, no JSX.
 */
( function ( wp ) {
	const el = wp.element.createElement;
	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel, store: editorStore } = wp.editor;
	const { TextControl } = wp.components;
	const { useSelect } = wp.data;
	const { useEntityProp } = wp.coreData;

	function PodcastPanel() {
		const postType = useSelect( ( select ) => select( editorStore ).getCurrentPostType(), [] );
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

		if ( 'post' !== postType || ! meta ) {
			return null;
		}

		const field = ( key, label, help, props ) =>
			el( TextControl, {
				__next40pxDefaultSize: true,
				__nextHasNoMarginBottom: true,
				label,
				help,
				value: meta[ key ] || '',
				onChange: ( value ) => setMeta( { ...meta, [ key ]: value } ),
				...props,
			} );

		return el(
			PluginDocumentSettingPanel,
			{ name: 'ihumbak-podcast', title: 'Podcast', className: 'ihumbak-podcast-panel' },
			el(
				'div',
				{ style: { display: 'grid', gap: '16px' } },
				field( 'sb_podcast_episode', 'Numer odcinka', 'Same cyfry, np. 112.', { inputMode: 'numeric' } ),
				field( 'sb_podcast_duration', 'Czas trwania', 'Tak, jak ma się wyświetlić, np. 38 min.', {} )
			)
		);
	}

	registerPlugin( 'ihumbak-podcast', { render: PodcastPanel } );
} )( window.wp );
