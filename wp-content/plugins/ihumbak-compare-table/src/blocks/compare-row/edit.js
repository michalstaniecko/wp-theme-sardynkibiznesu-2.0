import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

import { VALUE } from '../../utils/columns';

export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) {
	const { title, description } = attributes;

	const hasSelectedCell = useSelect(
		( select ) =>
			select( blockEditorStore ).hasSelectedInnerBlock( clientId, true ),
		[ clientId ]
	);

	const hasTitle = ! RichText.isEmpty( title );
	const hasDescription = ! RichText.isEmpty( description );
	const isActive = isSelected || hasSelectedCell;

	const blockProps = useBlockProps( { className: 'ihumbak-ct__row' } );

	// One value per service column, kept in step by the table (see
	// utils/columns.js); cells cannot be added, removed or moved on their own.
	const { children, ...innerBlocksProps } = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ VALUE ],
		templateLock: 'all',
		orientation: 'horizontal',
		renderAppender: false,
	} );

	return (
		<div { ...innerBlocksProps }>
			<div className="ihumbak-ct__feature">
				{ /* Both fields are optional; an empty one is only offered while the row is being edited. */ }
				{ ( hasTitle || isActive || ! hasDescription ) && (
					<RichText
						tagName="div"
						className="ihumbak-ct__feature-title"
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
						placeholder={ __(
							'Feature title (optional)',
							'ihumbak-compare-table'
						) }
					/>
				) }
				{ ( hasDescription || isActive ) && (
					<RichText
						tagName="div"
						className="ihumbak-ct__feature-description"
						value={ description }
						onChange={ ( value ) =>
							setAttributes( { description: value } )
						}
						placeholder={ __(
							'Description (optional)',
							'ihumbak-compare-table'
						) }
					/>
				) }
			</div>
			{ children }
		</div>
	);
}
