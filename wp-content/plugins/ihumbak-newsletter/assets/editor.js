/**
 * Editor of Formularz zapisu — no build, no JSX (spec #36 toolchain).
 * Title and text are inner blocks; the form itself is rendered in PHP,
 * the editor shows a static preview of the fields.
 */
( function ( blocks, blockEditor, components, element, i18n ) {
	const el = element.createElement;
	const { __ } = i18n;
	const { useBlockProps, useInnerBlocksProps, InnerBlocks, InspectorControls } = blockEditor;
	const { PanelBody, SelectControl, TextControl } = components;

	blocks.registerBlockType( 'ihumbak/newsletter-form', {
		edit( { attributes, setAttributes } ) {
			const { layout, fields, groupId, submitLabel } = attributes;
			const blockProps = useBlockProps( {
				className: 'ihumbak-newsletter-form--' + layout + ' ihumbak-newsletter-form--' + fields,
			} );
			const intro = useInnerBlocksProps(
				{ className: 'wp-block-ihumbak-newsletter-form__intro' },
				{
					template: [
						[ 'core/heading', { content: __( 'Zapisz się na newsletter', 'ihumbak-newsletter' ) } ],
						[
							'core/paragraph',
							{
								content: __(
									'Praktyczne porady dla małych firm: marketing, prawo i pieniądze. Raz na jakiś czas, prosto na Twoją skrzynkę.',
									'ihumbak-newsletter'
								),
							},
						],
					],
				}
			);

			const field = ( name, placeholder ) =>
				el(
					'p',
					{ className: 'wp-block-ihumbak-newsletter-form__field wp-block-ihumbak-newsletter-form__field--' + name },
					el( 'input', { type: 'name' === name ? 'text' : 'email', placeholder, disabled: true } )
				);

			return [
				el(
					InspectorControls,
					{ key: 'inspector' },
					el(
						PanelBody,
						{ title: __( 'Formularz', 'ihumbak-newsletter' ) },
						el( SelectControl, {
							label: __( 'Układ', 'ihumbak-newsletter' ),
							value: layout,
							options: [
								{ label: __( 'Sekcja (szeroki)', 'ihumbak-newsletter' ), value: 'section' },
								{ label: __( 'Pod artykułem (pola w kolumnie)', 'ihumbak-newsletter' ), value: 'stacked' },
								{ label: __( 'Kompaktowy (Kolumna boczna)', 'ihumbak-newsletter' ), value: 'callout' },
							],
							onChange: ( value ) => setAttributes( { layout: value } ),
							__nextHasNoMarginBottom: true,
						} ),
						el( SelectControl, {
							label: __( 'Pola', 'ihumbak-newsletter' ),
							value: fields,
							options: [
								{ label: __( 'Imię i e-mail', 'ihumbak-newsletter' ), value: 'name-email' },
								{ label: __( 'Tylko e-mail', 'ihumbak-newsletter' ), value: 'email' },
							],
							onChange: ( value ) => setAttributes( { fields: value } ),
							__nextHasNoMarginBottom: true,
						} ),
						el( TextControl, {
							label: __( 'Tekst przycisku', 'ihumbak-newsletter' ),
							value: submitLabel,
							placeholder: __( 'Zapisz się', 'ihumbak-newsletter' ),
							onChange: ( value ) => setAttributes( { submitLabel: value } ),
							__nextHasNoMarginBottom: true,
						} ),
						el( TextControl, {
							label: __( 'ID grupy MailerLite', 'ihumbak-newsletter' ),
							help: __( 'Puste = domyślna grupa z Ustawienia → Newsletter.', 'ihumbak-newsletter' ),
							value: groupId,
							inputMode: 'numeric',
							onChange: ( value ) => setAttributes( { groupId: value.replace( /\D/g, '' ) } ),
							__nextHasNoMarginBottom: true,
						} )
					)
				),
				el(
					'div',
					Object.assign( { key: 'block' }, blockProps ),
					el( 'div', intro ),
					el(
						'div',
						{ className: 'wp-block-ihumbak-newsletter-form__form' },
						'name-email' === fields ? field( 'name', attributes.namePlaceholder ) : null,
						field( 'email', attributes.emailPlaceholder ),
						el(
							'p',
							{ className: 'wp-block-ihumbak-newsletter-form__submit' },
							el(
								'span',
								{ className: 'wp-block-button__link wp-element-button' },
								submitLabel || __( 'Zapisz się', 'ihumbak-newsletter' )
							)
						)
					)
				),
			];
		},
		save() {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
