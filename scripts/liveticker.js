/**
 * Contructor of the scLiveticker object.
 *
 * @class
 */
( function() {
	var ajaxURL = sclivetickerAjax.ajax_url;
	var nonce = sclivetickerAjax.nonce;
	var pollInterval = sclivetickerAjax.poll_interval;
	var ticker;
	var widgets;

	/**
	 * Initialize iveticker JS component.
	 *
	 * @return {void}
	 */
	var init = function() {
		var updateNow = false;

		// Opt out if AJAX pobject not present.
		if ( 'undefined' === typeof sclivetickerAjax ) {
			return;
		}

		// Extract AJAX settings.
		ajaxURL = sclivetickerAjax.ajax_url;
		nonce = sclivetickerAjax.nonce;
		pollInterval = sclivetickerAjax.poll_interval;

		// Get ticker elements.
		ticker = [].map.call(
			document.querySelectorAll( 'div.wp-block-scliveticker-ticker.sclt-ajax' ),
			function( elem ) {
				var list = elem.querySelector( 'ul' );
				var last = Number( elem.getAttribute( 'data-sclt-last' ) );

				if ( ! list ) {
					list = document.createElement( 'ul' );
					elem.appendChild( list );
				}

				if ( 0 === last ) {
					updateNow = true;
				}

				return {
					s: elem.getAttribute( 'data-sclt-ticker' ),
					l: elem.getAttribute( 'data-sclt-limit' ),
					t: last,
					e: list,
				};
			}
		);

		// Get widget elements.
		widgets = [].map.call(
			document.querySelectorAll( 'div.wp-widget-scliveticker-ticker.sclt-ajax' ),
			function( elem ) {
				var list = elem.querySelector( 'ul' );
				var last = Number( elem.getAttribute( 'data-sclt-last' ) );

				if ( ! list ) {
					list = document.createElement( 'ul' );
					elem.appendChild( list );
				}

				if ( 0 === last ) {
					updateNow = true;
				}

				return {
					w: elem.getAttribute( 'data-sclt-ticker' ),
					l: elem.getAttribute( 'data-sclt-limit' ),
					t: last,
					e: list,
				};
			}
		);

		// Trigger update, if necessary.
		if ( ( 0 < ticker.length || widgets.length ) && 0 < pollInterval ) {
			if ( updateNow ) {
				update();
			} else {
				setTimeout( update, pollInterval );
			}
		}
	};

	/**
	 * Update liveticker on current page via AJAX call.
	 *
	 * @return {void}
	 */
	var update = function() {
		// Extract ticker-slug, limit and timestamp of last poll.
		var updateReq = 'action=sclt_update-ticks&_ajax_nonce=' + nonce;
		var i, j;
		var xhr = new XMLHttpRequest();

		for ( i = 0; i < ticker.length; i++ ) {
			updateReq = updateReq +
				'&update[' + i + '][s]=' + ticker[ i ].s +
				'&update[' + i + '][l]=' + ticker[ i ].l +
				'&update[' + i + '][t]=' + ticker[ i ].t;
		}
		for ( j = 0; j < widgets.length; j++ ) {
			updateReq = updateReq +
				'&update[' + ( i + j ) + '][w]=' + widgets[ j ].w +
				'&update[' + ( i + j ) + '][l]=' + widgets[ j ].l +
				'&update[' + ( i + j ) + '][t]=' + widgets[ j ].t;
		}

		// Issue AJAX request.
		xhr.open( 'POST', ajaxURL, true );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded;' );
		xhr.onreadystatechange = function() {
			var updateResp;
			if ( XMLHttpRequest.DONE === this.readyState && 200 === this.status ) {
				try {
					updateResp = JSON.parse( this.responseText );
					if ( updateResp ) {
						updateResp.forEach(
							function( u ) {
								ticker.forEach(
									function( t ) {
										if ( t.s === u.s ) {
											t.t = u.t;					// Update last poll timestamp.
											updateHTML( t, u );	// Update HTML markup.
										}
									}
								);
								widgets.forEach(
									function( t ) {
										if ( t.w === u.w ) {
											t.t = u.t;
											updateHTML( t, u );
										}
									}
								);
							}
						);
					}
					setTimeout( update, pollInterval );		// Re-trigger update.
				} catch ( e ) {
					// eslint-disable-next-line no-console
					console.warn( 'Liveticker AJAX update failed, stopping automatic updates.' );
				}
			}
		};
		xhr.send( updateReq );
	};

	/**
	 * Do actual update of HTML code.
	 *
	 * @param {Object}      t   Ticker or Widget reference.
	 * @param {number}      t.l Limit of entries to display.
	 * @param {HTMLElement} t.e HTML element of the ticker/widget.
	 * @param {Object}      u   Update entity.
	 * @param {string}      u.h HTML code to append.
	 * @param {number}      u.t Timetsamp of last update.
	 * @return {void}
	 */
	var updateHTML = function( t, u ) {
		// Parse new DOM-part.
		var n = document.createElement( 'ul' );
		n.innerHTML = u.h;

		// Prepend new ticks to container.
		while ( n.hasChildNodes() ) {
			t.e.prepend( n.lastChild );
		}

		t.e.parentNode.setAttribute( 'data-sclt-last', u.t );

		// Remove tail, if limit is set.
		if ( 0 < t.l ) {
			[].slice.call( t.e.getElementsByTagName( 'li' ), t.l ).forEach(
				function( li ) {
					li.remove();
				}
			);
		}
	};

	document.addEventListener(
		'DOMContentLoaded',
		function() {
			init();	// Trigger periodic update of livetickers.
		}
	);
}() );
