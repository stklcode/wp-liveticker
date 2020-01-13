/**
 * stklcode-liveticker Gutenberg Block
 *
 * Gutenberg Block to integrate the liveticker widget without shortcode.
 */
( function() {
	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var registerStore = wp.data.registerStore;
	var withSelect = wp.data.withSelect;
	var el = wp.element.createElement;

	/**
	 * Datastore actions.
	 */
	var actions = {
		setTickers: function( tickers ) {
			return {
				type: 'SET_TICKERS',
				tickers: tickers,
			};
		},
		getTickers: function( path ) {
			return {
				type: 'RECEIVE_TICKERS',
				path: path,
			};
		},
	};

	registerStore( 'scliveticker/ticker', {
		reducer: function( state, action ) {
			if ( undefined === state ) {
				state = { tickers: null };
			}
			switch ( action.type ) {
				case 'SET_TICKERS':
					state.tickers = action.tickers;
					return state;
				case 'RECEIVE_TICKERS':
					return action.tickers;
			}

			return state;
		},

		actions: actions,

		selectors: {
			receiveTickers: function( state ) {
				return state.tickers;
			},
		},

		resolvers: {
			receiveTickers: function() {
				return wp.apiFetch( { path: '/wp/v2/scliveticker_ticker' } ).then( function( tickers ) {
					return actions.setTickers( tickers.map( function( t ) {
						return {
							name: t.name,
							slug: t.slug,
						};
					} ) );
				} );
			},
		},
	} );

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
		edit: withSelect( function( select ) {
			return {
				tickers: select( 'scliveticker/ticker' ).receiveTickers(),
			};
		} )( function( props ) {
			var label = [
				el(
					wp.components.Dashicon,
					{ icon: 'rss' }
				),
				__( 'Liveticker', 'stklcode-liveticker' ),
			];
			var content;
			if ( null === props.tickers ) {
				// Tickers not yet loaded.
				content = [
					el(
						'span',
						{ className: 'components-base-control label' },
						label
					),
					el( wp.components.Spinner ),
				];
			} else if ( 0 === props.tickers.length ) {
				// No tickers available.
				content = [
					el(
						'span',
						{ className: 'components-base-control label' },
						label
					),
					el( 'span', null, __( 'No tickers available', 'stklcode-liveticker' ) ),
				];
			} else {
				// Tickers loaded and available.
				content = [
					el(
						wp.components.SelectControl,
						{
							label: label,
							value: props.attributes.ticker,
							options: props.tickers.map( function( t ) {
								return {
									value: t.slug,
									label: t.name,
								};
							} ),
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
				];
			}

			return el(
				'div',
				{ className: props.className + ' components-placeholder' },
				content
			);
		} ),
		save: function( props ) {
			return el(
				'div',
				{
					className: 'sclt-ajax',
					'data-sclt-ticker': props.attributes.ticker,
					'data-sclt-limit': props.attributes.unlimited ? 0 : props.attributes.limit,
					'data-sclt-last': 0,
				}
			);
		},
	} );
}() );
