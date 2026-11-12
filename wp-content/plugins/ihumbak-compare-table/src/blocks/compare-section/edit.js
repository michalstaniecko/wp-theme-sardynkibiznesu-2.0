import { __ } from '@wordpress/i18n';
import { useRegistry } from '@wordpress/data';
import {
	InspectorControls,
	RichText,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, ToggleControl } from '@wordpress/components';
import { plus } from '@wordpress/icons';

import { ROW, createRow, getColumnCount } from '../../utils/columns';
import { chevronIcon } from '../../utils/icons';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, defaultOpen } = attributes;
	const registry = useRegistry();
	const hasTitle = ! RichText.isEmpty( title );

	// Always expanded in the editor, whatever "open by default" says.
	const blockProps = useBlockProps( {
		className: 'ihumbak-ct__section is-open',
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'ihumbak-ct__section-body-inner' },
		{
			allowedBlocks: [ ROW ],
			renderAppender: false,
		}
	);

	const addRow = () => {
		const { getBlockRootClientId } = registry.select( blockEditorStore );
		const columnCount = getColumnCount(
			registry,
			getBlockRootClientId( clientId )
		);

		registry
			.dispatch( blockEditorStore )
			.insertBlock( createRow( columnCount ), undefined, clientId );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Section settings', 'ihumbak-compare-table' ) }
				>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Open by default',
							'ihumbak-compare-table'
						) }
						help={
							hasTitle
								? __(
										'Visitors can collapse and expand the section. It is always expanded in the editor.',
										'ihumbak-compare-table'
								  )
								: __(
										'A section without a title has no heading bar and is always open.',
										'ihumbak-compare-table'
								  )
						}
						checked={ hasTitle ? defaultOpen : true }
						disabled={ ! hasTitle }
						onChange={ ( value ) =>
							setAttributes( { defaultOpen: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div
					className={
						'ihumbak-ct__section-head' +
						( hasTitle
							? ''
							: ' ihumbak-ct-editor__section-head--empty' )
					}
				>
					<div className="ihumbak-ct__section-head-cell">
						<div className="ihumbak-ct__section-heading">
							<div className="ihumbak-ct__section-toggle">
								<RichText
									tagName="span"
									className="ihumbak-ct__section-title"
									value={ title }
									onChange={ ( value ) =>
										setAttributes( { title: value } )
									}
									placeholder={ __(
										'Section title (optional)',
										'ihumbak-compare-table'
									) }
									allowedFormats={ [] }
								/>
								{ hasTitle && (
									<span className="ihumbak-ct__section-toggle-icon">
										{ chevronIcon }
									</span>
								) }
							</div>
						</div>
					</div>
				</div>
				<div className="ihumbak-ct__section-body">
					<div { ...innerBlocksProps } />
				</div>
				<div className="ihumbak-ct-editor__appender">
					<Button
						variant="tertiary"
						size="small"
						icon={ plus }
						// Keep focus where it is: moving it would deselect the cell
						// being edited, collapse its empty fields and shift this
						// button from under the pointer before the click lands.
						onMouseDown={ ( event ) => event.preventDefault() }
						onClick={ addRow }
					>
						{ __( 'Add row', 'ihumbak-compare-table' ) }
					</Button>
				</div>
			</div>
		</>
	);
}
