import { __ } from '@wordpress/i18n';
import { useRegistry, useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import {
	BlockControls,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	TextControl,
	ToggleControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import {
	chevronLeft,
	chevronRight,
	tableColumnAfter,
	tableColumnBefore,
	tableColumnDelete,
} from '@wordpress/icons';

import {
	MAX_COLUMNS,
	MIN_COLUMNS,
	insertColumn,
	moveColumn,
	removeColumn,
} from '../../utils/columns';

const ALLOWED_MEDIA_TYPES = [ 'image' ];

export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) {
	const {
		name,
		price,
		priceNote,
		imageId,
		imageAlt,
		buttonText,
		buttonUrl,
		buttonNewTab,
		buttonRel,
	} = attributes;
	const registry = useRegistry();

	const { tableClientId, index, count } = useSelect(
		( select ) => {
			const { getBlockRootClientId, getBlockIndex, getBlockCount } =
				select( blockEditorStore );
			const headerClientId = getBlockRootClientId( clientId );

			return {
				tableClientId: getBlockRootClientId( headerClientId ),
				index: getBlockIndex( clientId ),
				count: getBlockCount( headerClientId ),
			};
		},
		[ clientId ]
	);

	// Only the attachment ID is stored; PHP prints the image with
	// wp_get_attachment_image(), the editor looks the URL up for the preview.
	const imageUrl = useSelect(
		( select ) => {
			if ( ! imageId ) {
				return '';
			}
			const media = select( coreStore ).getMedia( imageId, {
				context: 'view',
			} );

			return (
				media?.media_details?.sizes?.medium?.source_url ??
				media?.source_url ??
				''
			);
		},
		[ imageId ]
	);

	const onSelectImage = ( media ) =>
		setAttributes( {
			imageId: media?.id ?? 0,
			imageAlt: media?.alt ?? '',
		} );
	const onRemoveImage = () => setAttributes( { imageId: 0, imageAlt: '' } );

	const canAdd = count < MAX_COLUMNS;
	const canRemove = count > MIN_COLUMNS;
	const limitLabel = __(
		'A table can have at most 3 service columns',
		'ihumbak-compare-table'
	);

	const blockProps = useBlockProps( { className: 'ihumbak-ct__column' } );

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ chevronLeft }
						label={ __(
							'Move column left',
							'ihumbak-compare-table'
						) }
						disabled={ index <= 0 }
						accessibleWhenDisabled
						onClick={ () =>
							moveColumn(
								registry,
								tableClientId,
								index,
								index - 1
							)
						}
					/>
					<ToolbarButton
						icon={ chevronRight }
						label={ __(
							'Move column right',
							'ihumbak-compare-table'
						) }
						disabled={ index >= count - 1 }
						accessibleWhenDisabled
						onClick={ () =>
							moveColumn(
								registry,
								tableClientId,
								index,
								index + 1
							)
						}
					/>
				</ToolbarGroup>
				<ToolbarGroup>
					<ToolbarButton
						icon={ tableColumnBefore }
						label={
							canAdd
								? __(
										'Add column before',
										'ihumbak-compare-table'
								  )
								: limitLabel
						}
						disabled={ ! canAdd }
						accessibleWhenDisabled
						onClick={ () =>
							insertColumn( registry, tableClientId, index )
						}
					/>
					<ToolbarButton
						icon={ tableColumnAfter }
						label={
							canAdd
								? __(
										'Add column after',
										'ihumbak-compare-table'
								  )
								: limitLabel
						}
						disabled={ ! canAdd }
						accessibleWhenDisabled
						onClick={ () =>
							insertColumn( registry, tableClientId, index + 1 )
						}
					/>
					<ToolbarButton
						icon={ tableColumnDelete }
						label={
							canRemove
								? __( 'Remove column', 'ihumbak-compare-table' )
								: __(
										'The last column cannot be removed',
										'ihumbak-compare-table'
								  )
						}
						disabled={ ! canRemove }
						accessibleWhenDisabled
						onClick={ () =>
							removeColumn( registry, tableClientId, index )
						}
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Image', 'ihumbak-compare-table' ) }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ onSelectImage }
							allowedTypes={ ALLOWED_MEDIA_TYPES }
							value={ imageId }
							render={ ( { open } ) => (
								<div className="ihumbak-ct-editor__media-actions">
									<Button
										variant="secondary"
										onClick={ open }
									>
										{ imageId
											? __(
													'Replace image',
													'ihumbak-compare-table'
											  )
											: __(
													'Select image',
													'ihumbak-compare-table'
											  ) }
									</Button>
									{ !! imageId && (
										<Button
											variant="tertiary"
											isDestructive
											onClick={ onRemoveImage }
										>
											{ __(
												'Remove image',
												'ihumbak-compare-table'
											) }
										</Button>
									) }
								</div>
							) }
						/>
					</MediaUploadCheck>
					{ !! imageId && (
						<TextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __(
								'Alternative text',
								'ihumbak-compare-table'
							) }
							help={ __(
								'Leave empty to use the text stored with the image.',
								'ihumbak-compare-table'
							) }
							value={ imageAlt }
							onChange={ ( value ) =>
								setAttributes( { imageAlt: value } )
							}
						/>
					) }
				</PanelBody>
				<PanelBody title={ __( 'Button', 'ihumbak-compare-table' ) }>
					<TextControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						type="url"
						label={ __( 'Button URL', 'ihumbak-compare-table' ) }
						help={ __(
							'The button is shown only when a URL is set.',
							'ihumbak-compare-table'
						) }
						placeholder="https://"
						value={ buttonUrl }
						onChange={ ( value ) =>
							setAttributes( { buttonUrl: value } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Open in new tab',
							'ihumbak-compare-table'
						) }
						checked={ buttonNewTab }
						onChange={ ( value ) =>
							setAttributes( { buttonNewTab: value } )
						}
					/>
					<TextControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Link rel', 'ihumbak-compare-table' ) }
						help={ __(
							'Space-separated, e.g. "nofollow sponsored".',
							'ihumbak-compare-table'
						) }
						value={ buttonRel }
						onChange={ ( value ) =>
							setAttributes( { buttonRel: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ onSelectImage }
						allowedTypes={ ALLOWED_MEDIA_TYPES }
						value={ imageId }
						render={ ( { open } ) => {
							if ( imageUrl ) {
								return (
									<div className="ihumbak-ct__column-image">
										<button
											type="button"
											className="ihumbak-ct-editor__image-button"
											onClick={ open }
											aria-label={ __(
												'Replace image',
												'ihumbak-compare-table'
											) }
										>
											<img
												className="ihumbak-ct__column-img"
												src={ imageUrl }
												alt={ imageAlt }
											/>
										</button>
									</div>
								);
							}

							return isSelected && ! imageId ? (
								<div className="ihumbak-ct__column-image">
									<Button
										variant="secondary"
										size="small"
										onClick={ open }
									>
										{ __(
											'Add image',
											'ihumbak-compare-table'
										) }
									</Button>
								</div>
							) : null;
						} }
					/>
				</MediaUploadCheck>
				<RichText
					tagName="div"
					className="ihumbak-ct__column-name"
					value={ name }
					onChange={ ( value ) => setAttributes( { name: value } ) }
					placeholder={ __(
						'Service name',
						'ihumbak-compare-table'
					) }
				/>
				{ ( isSelected || ! RichText.isEmpty( price ) ) && (
					<RichText
						tagName="div"
						className="ihumbak-ct__column-price"
						value={ price }
						onChange={ ( value ) =>
							setAttributes( { price: value } )
						}
						placeholder={ __( 'Price', 'ihumbak-compare-table' ) }
					/>
				) }
				{ ( isSelected || ! RichText.isEmpty( priceNote ) ) && (
					<RichText
						tagName="div"
						className="ihumbak-ct__column-price-note"
						value={ priceNote }
						onChange={ ( value ) =>
							setAttributes( { priceNote: value } )
						}
						placeholder={ __(
							'Price note',
							'ihumbak-compare-table'
						) }
					/>
				) }
				{ ( isSelected ||
					! RichText.isEmpty( buttonText ) ||
					!! buttonUrl ) && (
					<div className="ihumbak-ct__column-button">
						<RichText
							tagName="span"
							className="button button-small ihumbak-ct__button"
							value={ buttonText }
							onChange={ ( value ) =>
								setAttributes( { buttonText: value } )
							}
							placeholder={ __(
								'Button text',
								'ihumbak-compare-table'
							) }
							allowedFormats={ [] }
						/>
					</div>
				) }
			</div>
		</>
	);
}
