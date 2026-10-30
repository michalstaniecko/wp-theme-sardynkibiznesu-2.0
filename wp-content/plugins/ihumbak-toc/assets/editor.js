/**
 * Block editor: after a save, copies the Kotwice that the server wrote into post_content
 * (includes/anchors.php) onto the open heading blocks. Without it the editor would send the
 * headings without ids again on the next save, and a renamed heading would get a new Kotwica.
 * No build (spec #36 toolchain).
 */
( function ( data, blocks ) {
	const { select, dispatch, subscribe } = data;
	let wasSaving = false;

	const headingsOf = ( list, out = [] ) => {
		list.forEach( ( block ) => {
			if ( block.name === 'core/heading' ) {
				out.push( block );
			}
			headingsOf( block.innerBlocks || [], out );
		} );
		return out;
	};

	const copyAnchors = () => {
		const post = select( 'core/editor' ).getCurrentPost();
		const content = post && typeof post.content === 'string' ? post.content : '';
		if ( content.indexOf( 'wp:heading' ) === -1 ) {
			return;
		}
		const saved = headingsOf( blocks.parse( content ) );
		const open = headingsOf( select( 'core/editor' ).getEditorBlocks() );
		// Pair by position; give up on any doubt (the post changed while it was saving).
		if ( saved.length !== open.length ) {
			return;
		}
		const blockEditor = dispatch( 'core/block-editor' );
		open.forEach( ( block, i ) => {
			const anchor = saved[ i ].attributes.anchor;
			if (
				block.attributes.anchor ||
				! anchor ||
				block.attributes.level !== saved[ i ].attributes.level ||
				String( block.attributes.content ) !== String( saved[ i ].attributes.content )
			) {
				return;
			}
			// The anchor is already saved: this change must not mark the post as edited.
			blockEditor.__unstableMarkNextChangeAsNotPersistent();
			blockEditor.updateBlockAttributes( block.clientId, { anchor } );
		} );
	};

	subscribe( () => {
		const editor = select( 'core/editor' );
		if ( ! editor || ! editor.getCurrentPostId() ) {
			return;
		}
		const saving = editor.isSavingPost() && ! editor.isAutosavingPost();
		if ( wasSaving && ! saving && editor.didPostSaveRequestSucceed() ) {
			wasSaving = false;
			copyAnchors();
			return;
		}
		wasSaving = saving;
	} );
} )( window.wp.data, window.wp.blocks );
