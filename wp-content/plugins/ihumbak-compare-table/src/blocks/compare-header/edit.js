import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

import { COLUMN } from '../../utils/columns';

export default function Edit( { clientId } ) {
	// The label of the feature column is a table attribute (the header has
	// none), edited here because this is where it is displayed.
	const { tableClientId, featureColumnLabel } = useSelect(
		( select ) => {
			const { getBlockRootClientId, getBlockAttributes } =
				select( blockEditorStore );
			const rootClientId = getBlockRootClientId( clientId );

			return {
				tableClientId: rootClientId,
				featureColumnLabel:
					getBlockAttributes( rootClientId )?.featureColumnLabel ??
					'',
			};
		},
		[ clientId ]
	);
	const { updateBlockAttributes } = useDispatch( blockEditorStore );

	const blockProps = useBlockProps( { className: 'ihumbak-ct__header' } );

	// Columns are added, removed and moved only through the table-level
	// commands in the column toolbar, which keep every row in step.
	const { children, ...innerBlocksProps } = useInnerBlocksProps(
		{ className: 'ihumbak-ct__header-row' },
		{
			allowedBlocks: [ COLUMN ],
			templateLock: 'all',
			orientation: 'horizontal',
			renderAppender: false,
		}
	);

	return (
		<div { ...blockProps }>
			<div { ...innerBlocksProps }>
				<div className="ihumbak-ct__corner">
					<RichText
						tagName="span"
						className="ihumbak-ct__corner-label"
						value={ featureColumnLabel }
						onChange={ ( value ) =>
							updateBlockAttributes( tableClientId, {
								featureColumnLabel: value,
							} )
						}
						placeholder={ __(
							'Feature column label',
							'ihumbak-compare-table'
						) }
						allowedFormats={ [] }
					/>
				</div>
				{ children }
			</div>
		</div>
	);
}
