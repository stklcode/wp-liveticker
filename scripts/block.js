/**
 * stklcode-liveticker Gutenberg Block
 *
 * Gutenberg Block to integrate the liveticker widget without shortcode.
 */
( function() {
	var { __ } = wp.i18n;
	var { registerBlockType } = wp.blocks;
	var { registerStore, withSelect } = wp.data;
	var el = wp.element.createElement;

	/**
	 * Datastore actions.
	 */
	var actions = {
		setTickers( tickers ) {
			return {
				type: 'SET_TICKERS',
				tickers,
			};
		},
		getTickers( path ) {
			return {
				type: 'RECEIVE_TICKERS',
				path,
			};
		},
		loadTickers( path ) {
			return {
				type: 'LOAD_TICKERS',
				path,
			};
		},
	};

	registerStore( 'scliveticker/ticker', {
		reducer( state = { tickers: null }, action ) {
			switch ( action.type ) {
				case 'SET_TICKERS':
					return {
						...state,
						tickers: action.tickers,
					};
				case 'RECEIVE_TICKERS':
					return action.tickers;
			}

			return state;
		},

		actions,

		selectors: {
			receiveTickers( state ) {
				const { tickers } = state;
				return tickers;
			},
		},

		controls: {
			LOAD_TICKERS( action ) {
				return wp.apiFetch( { path: action.path } );
			},
		},

		resolvers: {
			* receiveTickers() {
				const tickers = yield actions.loadTickers( '/wp/v2/scliveticker_ticker' );
				return actions.setTickers( tickers.map( function( t ) {
					return {
						name: t.name,
						slug: t.slug,
					};
				} ) );
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
		edit: withSelect( ( select ) => {
			return {
				tickers: select( 'scliveticker/ticker' ).receiveTickers(),
			};
		} )( function( props ) {
			var content;
			if ( null === props.tickers ) {
				// Tickers not yet loaded.
				content = el( wp.components.Spinner );
			} else if ( 0 === props.length ) {
				// No tickers available.
				content = el( 'p', null, 'No tickers available' );
			} else {
				// Tickers loaded and available.
				content = [
					el(
						wp.components.SelectControl,
						{
							label: [
								el(
									wp.components.Dashicon,
									{ icon: 'rss' }
								),
								__( 'Liveticker', 'stklcode-liveticker' ),
							],
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
					className: props.className + ' sclt-ajax',
					'data-sclt-ticker': props.attributes.ticker,
					'data-sclt-limit': props.attributes.unlimited ? 0 : props.attributes.limit,
					'data-sclt-last': 0,
				}
			);
		},
	} );
}() );
