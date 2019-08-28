/**
 * Contructor of the scLiveticker object.
 *
 * @constructor
 */
function scLiveticker() {
}

/**
 * Initialize iveticker JS component.
 *
 * @return {void}
 */
scLiveticker.init = function() {
	// Opt out if AJAX pobject not present.
	if ( 'undefined' === typeof sclivetickerAjax ) {
		return;
	}

	// Extract AJAX settings.
	scLiveticker.ajaxURL = sclivetickerAjax.ajax_url;
	scLiveticker.nonce = sclivetickerAjax.nonce;
	scLiveticker.pollInterval = sclivetickerAjax.poll_interval;

	// Get ticker elements.
	scLiveticker.ticker = [].map.call(
		document.querySelectorAll( 'ul.sclt-ticker-ajax' ),
		function( elem ) {
			return {
				s: elem.getAttribute( 'data-sclt-ticker' ),
				l: elem.getAttribute( 'data-sclt-limit' ),
				t: elem.getAttribute( 'data-sclt-last' ),
				e: elem,
			};
		}
	);

	// Get widget elements.
	scLiveticker.widgets = [].map.call(
		document.querySelectorAll( 'ul.sclt-widget-ajax' ),
		function( elem ) {
			return {
				w: elem.getAttribute( 'data-sclt-ticker' ),
				l: elem.getAttribute( 'data-sclt-limit' ),
				t: elem.getAttribute( 'data-sclt-last' ),
				e: elem,
			};
		}
	);

	// Trigger update, if necessary.
	if ( ( 0 < scLiveticker.ticker.length || scLiveticker.widgets.length ) && 0 < scLiveticker.pollInterval ) {
		setTimeout( scLiveticker.update, scLiveticker.pollInterval );
	}
};

/**
 * Update liveticker on current page via AJAX call.
 *
 * @return {void}
 */
scLiveticker.update = function() {
	// Extract ticker-slug, limit and timestamp of last poll.
	var updateReq = 'action=sclt_update-ticks&_ajax_nonce=' + scLiveticker.nonce;
	var i, j;
	var xhr = new XMLHttpRequest();

	for ( i = 0; i < scLiveticker.ticker.length; i++ ) {
		updateReq = updateReq +
			'&update[' + i + '][s]=' + scLiveticker.ticker[ i ].s +
			'&update[' + i + '][l]=' + scLiveticker.ticker[ i ].l +
			'&update[' + i + '][t]=' + scLiveticker.ticker[ i ].t;
	}
	for ( j = 0; j < scLiveticker.widgets.length; j++ ) {
		updateReq = updateReq +
			'&update[' + ( i + j ) + '][w]=' + scLiveticker.widgets[ j ].w +
			'&update[' + ( i + j ) + '][l]=' + scLiveticker.widgets[ j ].l +
			'&update[' + ( i + j ) + '][t]=' + scLiveticker.widgets[ j ].t;
	}

	// Issue AJAX request.
	xhr.open( 'POST', scLiveticker.ajaxURL, true );
	xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded;' );
	xhr.onreadystatechange = function() {
		var update;
		if ( XMLHttpRequest.DONE === this.readyState && 200 === this.status ) {
			try {
				update = JSON.parse( this.responseText );
				if ( update ) {
					update.forEach(
						function( u ) {
							scLiveticker.ticker.forEach(
								function( t ) {
									if ( t.s === u.s ) {
										t.t = u.t;					// Update last poll timestamp.
										scLiveticker.updateHTML( t, u );	// Update HTML markup.
									}
								}
							);
							scLiveticker.widgets.forEach(
								function( t ) {
									if ( t.w === u.w ) {
										t.t = u.t;
										scLiveticker.updateHTML( t, u );
									}
								}
							);
						}
					);
				}
				setTimeout( scLiveticker.update, scLiveticker.pollInterval );		// Re-trigger update.
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
scLiveticker.updateHTML = function( t, u ) {
	// Prepend HTML of new ticks.
	t.e.innerHTML = u.h + t.e.innerHTML;
	t.e.setAttribute( 'data-sclt-last', u.t );

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
		scLiveticker.init();	// Trigger periodic update of livetickers.
	}
);
