import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';

import {
	VALUE_ICONS,
	checkIcon,
	infoIcon,
	minusIcon,
	noIcon,
} from '../../utils/icons';

export default function Edit( { attributes, setAttributes, isSelected } ) {
	const { icon, text, tooltip } = attributes;

	const ICON_OPTIONS = [
		{
			value: 'none',
			label: __( 'No icon', 'ihumbak-compare-table' ),
			icon: noIcon,
		},
		{
			value: 'check',
			label: __( 'Check (yes)', 'ihumbak-compare-table' ),
			icon: checkIcon,
		},
		{
			value: 'minus',
			label: __( 'Minus (no)', 'ihumbak-compare-table' ),
			icon: minusIcon,
		},
	];

	const hasIcon = icon === 'check' || icon === 'minus';
	const hasTooltip = ! RichText.isEmpty( tooltip );

	const blockProps = useBlockProps( { className: 'ihumbak-ct__value' } );

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					{ ICON_OPTIONS.map( ( option ) => (
						<ToolbarButton
							key={ option.value }
							icon={ option.icon }
							label={ option.label }
							isPressed={ icon === option.value }
							onClick={ () =>
								setAttributes( { icon: option.value } )
							}
						/>
					) ) }
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Value', 'ihumbak-compare-table' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Icon', 'ihumbak-compare-table' ) }
						value={ icon }
						options={ ICON_OPTIONS.map( ( { value, label } ) => ( {
							value,
							label,
						} ) ) }
						onChange={ ( value ) =>
							setAttributes( { icon: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ hasIcon && (
					<span
						className={ `ihumbak-ct__value-icon ihumbak-ct__value-icon--${ icon }` }
					>
						{ VALUE_ICONS[ icon ] }
					</span>
				) }
				{ ( isSelected || ! RichText.isEmpty( text ) || ! hasIcon ) && (
					<RichText
						tagName="span"
						className="ihumbak-ct__value-text"
						value={ text }
						onChange={ ( value ) =>
							setAttributes( { text: value } )
						}
						placeholder={
							isSelected
								? __( 'Value', 'ihumbak-compare-table' )
								: '…'
						}
					/>
				) }
				{ ( isSelected || hasTooltip ) && (
					<div className="ihumbak-ct-editor__tooltip">
						<span className="ihumbak-ct-editor__tooltip-icon">
							{ infoIcon }
						</span>
						<RichText
							tagName="span"
							className="ihumbak-ct-editor__tooltip-text"
							value={ tooltip }
							onChange={ ( value ) =>
								setAttributes( { tooltip: value } )
							}
							placeholder={ __(
								'Tooltip (optional)',
								'ihumbak-compare-table'
							) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
					</div>
				) }
			</div>
		</>
	);
}
