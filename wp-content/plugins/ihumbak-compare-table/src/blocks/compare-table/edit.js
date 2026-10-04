import { __, sprintf } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { useRegistry, useSelect } from '@wordpress/data';
import {
	BlockControls,
	InspectorControls,
	RichText,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	SelectControl,
	ToggleControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { plus, tableColumnAfter } from '@wordpress/icons';

import {
	COLUMN,
	HEADER,
	MAX_COLUMNS,
	ROW,
	SECTION,
	VALUE,
	createSection,
	getColumnCount,
	getInconsistency,
	getTableParts,
	insertColumn,
	reconcileTable,
	setColumnCount,
} from '../../utils/columns';

const TEMPLATE = [
	[
		HEADER,
		// The table always has exactly one header, kept first.
		{ lock: { move: true, remove: true } },
		[ [ COLUMN ], [ COLUMN ] ],
	],
	[ SECTION, {}, [ [ ROW, {}, [ [ VALUE ], [ VALUE ] ] ] ] ],
];

// Offered column counts. A table can still be brought down to one column
// with the column toolbar.
const COLUMN_COUNTS = [ 2, 3, 4 ].filter( ( count ) => count <= MAX_COLUMNS );

const HEADING_LEVELS = [ 2, 3, 4, 5, 6 ].map( ( level ) => ( {
	value: String( level ),
	label: `H${ level }`,
} ) );

export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) {
	// featureColumnLabel is edited in the header, where it is displayed.
	const { caption, stickyHeader, headingLevel } = attributes;
	const registry = useRegistry();

	const { columnCount, inconsistency } = useSelect(
		( select ) => {
			const blocks = select( blockEditorStore ).getBlocks( clientId );
			const { header } = getTableParts( blocks );

			return {
				columnCount: header ? header.innerBlocks.length : 0,
				inconsistency: getInconsistency( blocks ),
			};
		},
		[ clientId ]
	);

	useEffect( () => {
		if ( inconsistency ) {
			reconcileTable( registry, clientId );
		}
	}, [ inconsistency, registry, clientId ] );

	const cols = Math.min( Math.max( columnCount, 1 ), MAX_COLUMNS );

	const blockProps = useBlockProps( {
		className: [
			'ihumbak-ct',
			`ihumbak-ct--cols-${ cols }`,
			stickyHeader && 'ihumbak-ct--sticky',
		]
			.filter( Boolean )
			.join( ' ' ),
		style: { '--ct-cols': cols },
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'ihumbak-ct__table' },
		{
			// The header comes from the template only; editors add sections.
			allowedBlocks: [ SECTION ],
			template: TEMPLATE,
			renderAppender: false,
		}
	);

	const { insertBlock } = registry.dispatch( blockEditorStore );
	const addSection = () =>
		insertBlock(
			createSection( getColumnCount( registry, clientId ) ),
			undefined,
			clientId
		);

	const canAddColumn = columnCount > 0 && columnCount < MAX_COLUMNS;

	// The header is the source of truth: the control only reflects its column
	// count, including one that is not among the offered ones.
	const columnCountOptions = [
		...( COLUMN_COUNTS.includes( columnCount )
			? []
			: [
					{
						value: String( columnCount ),
						label: String( columnCount ),
						disabled: true,
					},
			  ] ),
		...COLUMN_COUNTS.map( ( count ) => ( {
			value: String( count ),
			label: String( count ),
		} ) ),
	];

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ tableColumnAfter }
						label={
							canAddColumn
								? __(
										'Add service column',
										'ihumbak-compare-table'
								  )
								: sprintf(
										/* translators: %d: maximum number of service columns. */
										__(
											'A table can have at most %d service columns',
											'ihumbak-compare-table'
										),
										MAX_COLUMNS
								  )
						}
						disabled={ ! canAddColumn }
						accessibleWhenDisabled
						onClick={ () =>
							insertColumn( registry, clientId, columnCount )
						}
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={ __( 'Table settings', 'ihumbak-compare-table' ) }
				>
					{ columnCount > 0 && (
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __(
								'Service columns',
								'ihumbak-compare-table'
							) }
							help={ __(
								'Columns are added and removed at the end of the table.',
								'ihumbak-compare-table'
							) }
							value={ String( columnCount ) }
							options={ columnCountOptions }
							onChange={ ( value ) =>
								setColumnCount(
									registry,
									clientId,
									Number( value )
								)
							}
						/>
					) }
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Sticky header', 'ihumbak-compare-table' ) }
						help={ __(
							'Keeps the service names visible while scrolling. Not previewed in the editor.',
							'ihumbak-compare-table'
						) }
						checked={ stickyHeader }
						onChange={ ( value ) =>
							setAttributes( { stickyHeader: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __(
							'Section heading level',
							'ihumbak-compare-table'
						) }
						value={ String( headingLevel ) }
						options={ HEADING_LEVELS }
						onChange={ ( value ) =>
							setAttributes( { headingLevel: Number( value ) } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ /* The caption has no sidebar field: it is typed here, where it
				     is displayed, and announced as the table's name. */ }
				{ ( isSelected || ! RichText.isEmpty( caption ) ) && (
					<RichText
						tagName="div"
						className="ihumbak-ct__caption"
						value={ caption }
						onChange={ ( value ) =>
							setAttributes( { caption: value } )
						}
						placeholder={ __(
							'Table caption (optional)',
							'ihumbak-compare-table'
						) }
						allowedFormats={ [] }
					/>
				) }
				<div { ...innerBlocksProps } />
				<div className="ihumbak-ct-editor__appender">
					<Button
						variant="secondary"
						icon={ plus }
						// Keep focus where it is: moving it would deselect the cell
						// being edited, collapse its empty fields and shift this
						// button from under the pointer before the click lands.
						onMouseDown={ ( event ) => event.preventDefault() }
						onClick={ addSection }
					>
						{ __( 'Add section', 'ihumbak-compare-table' ) }
					</Button>
				</div>
			</div>
		</>
	);
}
