/**
 * stklcode-liveticker Gutenberg Block
 *
 * Gutenberg Block to integrate the liveticker widget without shortcode.
 */
{
	const __ = wp.i18n.__;
	const registerBlockType = wp.blocks.registerBlockType;
	const registerStore = wp.data.registerStore;
	const withSelect = wp.data.withSelect;
	const el = wp.element.createElement;

	/**
	 * Datastore actions.
	 */
	const actions = {
		setTickers(tickers) {
			return {
				type: 'SET_TICKERS',
				tickers,
			};
		},
		getTickers(path) {
			return {
				type: 'RECEIVE_TICKERS',
				path,
			};
		},
	};

	registerStore('scliveticker/ticker', {
		reducer(state, action) {
			if (undefined === state) {
				state = { tickers: null };
			}
			switch (action.type) {
				case 'SET_TICKERS':
					state.tickers = action.tickers;
					return state;
				case 'RECEIVE_TICKERS':
					return action.tickers;
			}

			return state;
		},

		actions,

		selectors: {
			receiveTickers(state) {
				return state.tickers;
			},
		},

		resolvers: {
			receiveTickers() {
				return wp
					.apiFetch({ path: '/wp/v2/scliveticker_ticker' })
					.then((tickers) =>
						actions.setTickers(
							tickers.map((t) => {
								return {
									name: t.name,
									slug: t.slug,
								};
							})
						)
					);
			},
		},
	});

	registerBlockType('scliveticker/ticker', {
		title: __('Liveticker', 'stklcode-liveticker'),
		icon: 'rss',
		category: 'widgets',
		keywords: [__('Liveticker', 'stklcode-liveticker')],
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
			sort: {
				type: 'string',
				// implicit default: 'desc', left empty here for backwards compatibility of the block
			},
		},
		edit: withSelect((select) => {
			return {
				tickers: select('scliveticker/ticker').receiveTickers(),
			};
		})((props) => {
			const label = [
				el(wp.components.Dashicon, { icon: 'rss' }),
				__('Liveticker', 'stklcode-liveticker'),
			];
			let content;
			if (null === props.tickers) {
				// Tickers not yet loaded.
				content = [
					el(
						'span',
						{ className: 'components-base-control label' },
						label
					),
					el(wp.components.Spinner),
				];
			} else if (0 === props.tickers.length) {
				// No tickers available.
				content = [
					el(
						'span',
						{ className: 'components-base-control label' },
						label
					),
					el(
						'span',
						null,
						__('No tickers available', 'stklcode-liveticker')
					),
				];
			} else {
				// Tickers loaded and available.
				if (
					0 === props.attributes.ticker.length &&
					props.tickers.length > 0
				) {
					props.attributes.ticker = props.tickers[0].slug;
				}
				content = [
					el(wp.components.SelectControl, {
						label,
						value: props.attributes.ticker,
						options: props.tickers.map((t) => {
							return {
								value: t.slug,
								label: t.name,
							};
						}),
						onChange(val) {
							props.setAttributes({ ticker: val });
						},
					}),
					el(wp.components.TextControl, {
						label: __('Number of Ticks', 'stklcode-liveticker'),
						type: 'number',
						min: 1,
						step: 1,
						disabled: props.attributes.unlimited,
						value: props.attributes.limit,
						onChange(val) {
							props.setAttributes({ limit: Number(val) });
						},
					}),
					el(wp.components.CheckboxControl, {
						label: __('unlimited', 'stklcode-liveticker'),
						checked: props.attributes.unlimited,
						onChange(val) {
							props.setAttributes({ unlimited: val });
						},
					}),
					el(wp.components.SelectControl, {
						label: __('Output direction', 'stklcode-liveticker'),
						value: props.attributes.sort,
						options: [
							{
								value: 'desc',
								label: __(
									'newest first',
									'stklcode-liveticker'
								),
							},
							{
								value: 'asc',
								label: __(
									'oldest first',
									'stklcode-liveticker'
								),
							},
						],
						onChange(val) {
							props.setAttributes({ sort: val });
						},
					}),
				];
			}

			return el(
				'div',
				{ className: props.className + ' components-placeholder' },
				content
			);
		}),
		save(props) {
			return el('div', {
				className: 'sclt-ajax',
				'data-sclt-ticker': props.attributes.ticker,
				'data-sclt-limit': props.attributes.unlimited
					? 0
					: props.attributes.limit,
				'data-sclt-last': 0,
				'data-sclt-sort': props.attributes.sort,
			});
		},
	});
}
