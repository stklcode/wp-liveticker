/**
 * Contructor of the scLiveticker object.
 *
 * @class
 */
( function() {
	var apiURL;
	var pollInterval;
	var ticker;

	/**
	 * Initialize iveticker JS component.
	 *
	 * @return {void}
	 */
	var init = function() {
		var updateNow = false;
		var c = 0;

		// Opt out if AJAX pobject not present.
		if ( 'undefined' === typeof scliveticker ) {
			return;
		}

		// Extract settings.
		apiURL = scliveticker.api + 'wp/v2/scliveticker_tick';
		pollInterval = scliveticker.poll_interval;

		// Get ticker elements.
		ticker = [].map.call(
			document.querySelectorAll( 'div.wp-block-scliveticker-ticker.sclt-ajax' ),
			function( elem ) {
				var o = parseElement( elem, false, ++c );
				if ( '0' === o.lastPoll ) {
					updateNow = true;
				}
				return o;
			}
		);

		// Get widget elements.
		ticker.concat(
			[].map.call(
				document.querySelectorAll( 'div.wp-widget-scliveticker-ticker.sclt-ajax' ),
				function( elem ) {
					var o = parseElement( elem, true, ++c );
					if ( '0' === o.lastPoll ) {
						updateNow = true;
					}
					return o;
				}
			)
		);

		// Trigger update, if necessary.
		if ( ( 0 < ticker.length ) && 0 < pollInterval ) {
			if ( updateNow ) {
				update();
			} else {
				setTimeout( update, pollInterval );
			}
		}
	};

	/**
	 * Parse an HTML element containing a liveticker.
	 *
	 * @param {HTMLElement} elem   The element.
	 * @param {boolean}     widget Is the element a widget?
	 * @param {number}      n      Number of the container.
	 * @return {{ticker: string, lastPoll: string, ticks: any, limit: string, isWidget: *}} Ticker descriptor object.
	 */
	var parseElement = function( elem, widget, n ) {
		var list = elem.querySelector( 'ul' );
		var last = elem.getAttribute( 'data-sclt-last' );

		elem.id = 'sclt-' + n;

		if ( ! list ) {
			list = document.createElement( 'ul' );
			elem.appendChild( list );
		} else {
			[].forEach.call(
				elem.querySelectorAll( 'li.sclt-tick' ),
				function( li ) {
					var id = li.getAttribute( 'data-sclt-tick-id' );
					if ( id ) {
						li.id = 'sclt-' + n + '-' + id;
						li.removeAttribute( 'data-sclt-tick-id' );
					}
				}
			);
		}

		return {
			id: n,
			ticker: elem.getAttribute( 'data-sclt-ticker' ),
			limit: elem.getAttribute( 'data-sclt-limit' ),
			lastPoll: last,
			ticks: list,
			isWidget: widget,
			updating: false,
		};
	};

	/**
	 * Update liveticker on current page via REST API call.
	 *
	 * @return {void}
	 */
	var update = function() {
		// Iterate over available tickers.
		ticker.forEach(
			function( t ) {
				var xhr, query;

				if ( t.updating ) {
					// Do not update twice.
					return;
				}

				t.updating = true;
				xhr = new XMLHttpRequest();
				query = '?ticker=' + encodeURI( t.ticker ) +
					'&limit=' + encodeURI( t.limit ) +
					'&last=' + encodeURI( t.lastPoll );
				xhr.open( 'GET', apiURL + query, true );
				xhr.addEventListener(
					'load',
					function() {
						var updateResp;
						try {
							updateResp = JSON.parse( this.responseText );
							if ( updateResp ) {
								updateResp.reverse();
								updateResp.forEach(
									function( u ) {
										addTick( t, u );
									}
								);
							}
							t.updating = false;
						} catch ( e ) {
							// eslint-disable-next-line no-console
							console.warn( 'Liveticker AJAX update failed, stopping automatic updates.' );
						}
					}
				);
				xhr.send();
			}
		);

		// Re-trigger update.
		setTimeout( update, pollInterval );
	};

	/**
	 * Do actual update of HTML code.
	 *
	 * @param {Object} t Ticker or Widget reference.
	 * @param {Object} u Update entity.
	 * @return {void}
	 */
	var addTick = function( t, u ) {
		// Parse new DOM-part.
		var li = document.createElement( 'li' );
		var time = document.createElement( 'span' );
		var title = document.createElement( 'span' );
		var content = document.createElement( 'div' );
		var cls = t.isWidget ? 'sclt-widget' : 'sclt-tick';
		var old;
		var scripts = [];

		time.classList.add( cls + '-time' );
		time.innerText = u.modified_rendered;
		title.classList.add( cls + '-title' );
		title.innerText = u.title.rendered;
		content.classList.add( cls + '-content' );
		content.innerHTML = u.content.rendered;

		// Process embedded scripts, if any.
		if ( scliveticker.embedded_script ) {
			Array.prototype.forEach.call(
				content.getElementsByTagName( 'script' ),
				function( script ) {
					var script2;
					if ( script.src ) {
						// Move referenced scripts to page head.
						script.parentNode.removeChild( script );
						script2 = document.createElement( 'script' );
						Array.prototype.forEach.call( script.attributes, function( a ) {
							script2.setAttribute( a.nodeName, a.nodeValue );
						} );
						document.head.appendChild( script2 );
					} else {
						scripts.push( script );
					}
				}
			);
		}

		// Create the actual tick element.
		li.id = 'sclt-' + t.id + '-' + u.id;
		li.classList.add( cls );
		li.appendChild( time );
		li.appendChild( title );
		li.appendChild( content );

		old = document.getElementById( 'sclt-' + t.id + '-' + u.id );
		if ( old ) {
			// Replace entry, if it already exists (i.e. has been updated).
			t.ticks.replaceChild( li, old );
		} else {
			// Prepend new tick to container.
			t.ticks.insertBefore( li, t.ticks.firstChild );
		}

		// Update last poll time.
		t.lastPoll = u.date_gmt;
		t.ticks.parentNode.setAttribute( 'data-sclt-last', u.date_gmt );

		// Remove tail, if limit is set.
		if ( 0 < t.limit ) {
			[].slice.call( t.ticks.getElementsByTagName( 'li' ), t.limit ).forEach(
				function( l ) {
					l.remove();
				}
			);
		}

		// Evaluate embedded inline scripts.

		// Directly evaluate script otherwise.
		scripts.forEach( function( script ) {
			try {
				// eslint-disable-next-line no-eval
				eval( script.innerHTML );
			} catch ( e ) {
				// eslint-disable-next-line no-console
				console.warn( 'Failed to evaluate embedded script.' );
			}
		} );
	};

	document.addEventListener(
		'DOMContentLoaded',
		function() {
			init();	// Trigger periodic update of livetickers.
		}
	);
}() );
