/**
 * stklcode-liveticker Gutenberg Block
 *
 * Gutenberg Block to integrate the liveticker widget without shortcode.
 */
( function() {
	var { __ } = wp.i18n;
	var { registerBlockType } = wp.blocks;
	var el = wp.element.createElement;

	registerBlockType( 'scliveticker/ticker', {
		title: __( 'Liveticker', 'stklcode-liveticker' ),
		icon: 'rss',
		category: 'widgets',
		keywords: [
			__( 'Liveticker', 'stklcode-liveticker' ),
		],
		attributes: {
			ticker: {
				type: 'string',
				default: '',
			},
			limit: {
				type: 'number',
				default: 5,
			},
			unlimited: {
				type: 'boolean',
				default: false,
			},
		},
		edit: function( props ) {
			return el(
				'div',
				{ className: props.className + ' components-placeholder' },
				[
					el(
						wp.components.TextControl,
						{
							label: [
								el(
									wp.components.Dashicon,
									{ icon: 'rss' }
								),
								__( 'Liveticker', 'stklcode-liveticker' ) ],
							value: props.attributes.ticker,
							onChange: function( val ) {
								props.setAttributes( { ticker: val } );
							},
						}
					),
					el(
						wp.components.TextControl,
						{
							label: __( 'Number of Ticks', 'stklcode-liveticker' ),
							type: 'number',
							min: 1,
							step: 1,
							disabled: props.attributes.unlimited,
							value: props.attributes.limit,
							onChange: function( val ) {
								props.setAttributes( { limit: val } );
							},
						}
					),
					el(
						wp.components.CheckboxControl,
						{
							label: __( 'unlimited', 'stklcode-liveticker' ),
							checked: props.attributes.unlimited,
							onChange: function( val ) {
								props.setAttributes( { unlimited: val } );
							},
						}
					),
				],
			);
		},
		save: function( props ) {
			return el(
				'div',
				{
					className: props.className + ' sclt-ajax',
					'data-sclt-ticker': props.attributes.ticker,
					'data-sclt-limit': props.attributes.unlimited ? 0 : props.attributes.limit,
					'data-sclt-last': 0,
				}
			);
		},
	} );
}() );
