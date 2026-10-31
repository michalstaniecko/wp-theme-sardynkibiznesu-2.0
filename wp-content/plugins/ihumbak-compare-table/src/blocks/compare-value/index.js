import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';

// Leaf block: the content lives in the attributes and PHP renders it.
registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
