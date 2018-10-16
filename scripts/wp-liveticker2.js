function WPLT2() {

}

/**
 * Initialize WP-Liveticker 2 JS component.
 *
 * @return void
 */
WPLT2.init = function () {
	// Get ticker elements.
	WPLT2.ticker = [].map.call(
		document.querySelectorAll( 'ul.wplt2-ticker-ajax' ),
		function( elem ) {
			return {
				s: elem.getAttribute( 'data-wplt2-ticker' ),
				l: elem.getAttribute( 'data-wplt2-limit' ),
				t: elem.getAttribute( 'data-wplt2-last' ),
				e: elem
			};
		}
	);

	// Get widget elements.
	WPLT2.widgets = [].map.call(
		document.querySelectorAll( 'ul.wplt2-widget-ajax' ),
		function( elem ) {
			return {
				w: elem.getAttribute( 'data-wplt2-ticker' ),
				l: elem.getAttribute( 'data-wplt2-limit' ),
				t: elem.getAttribute( 'data-wplt2-last' ),
				e: elem
			};
		}
	);

	// Extract AJAX settings.
	WPLT2.ajaxURL = ajax_object.ajax_url;
	WPLT2.nonce = ajax_object.nonce;
	WPLT2.pollInterval = ajax_object.poll_interval;

	// Trigger update, if necessary.
	if ( ( WPLT2.ticker.length > 0 || WPLT2.widgets.length ) && WPLT2.pollInterval > 0 ) {
		setTimeout( WPLT2.update, WPLT2.pollInterval );
	}
};

/**
 * Update liveticker on current page via AJAX call.
 *
 * @return void
 */
WPLT2.update = function () {
	// Extract ticker-slug, limit and timestamp of last poll.
	var updateReq = 'action=wplt2_update-ticks&_ajax_nonce=' + WPLT2.nonce;
	var i;
	var j;

	for ( i = 0; i < WPLT2.ticker.length; i++ ) {
		updateReq = updateReq +
			'&update[' + i + '][s]=' + WPLT2.ticker[ i ].s +
			'&update[' + i + '][l]=' + WPLT2.ticker[ i ].l +
			'&update[' + i + '][t]=' + WPLT2.ticker[ i ].t;
	}
	for ( j = 0; j < WPLT2.widgets.length; j++ ) {
		updateReq = updateReq +
			'&update[' + ( i + j ) + '][w]=' + WPLT2.widgets[ j ].w +
			'&update[' + ( i + j ) + '][l]=' + WPLT2.widgets[ j ].l +
			'&update[' + ( i + j ) + '][t]=' + WPLT2.widgets[ j ].t;
	}

	// Issue AJAX request.
	var xhr = new XMLHttpRequest();
	xhr.open( 'POST', WPLT2.ajaxURL, true );
	xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded;' );
	xhr.onreadystatechange = function() {
		if( XMLHttpRequest.DONE === this.readyState && 200 === this.status ) {
			try {
				var update = JSON.parse( this.responseText );
				if ( update ) {
					update.forEach(
						function( u ) {
							WPLT2.ticker.forEach(
								function( t ) {
									if ( t.s === u.s ) {
										// Update last poll timestamp.
										t.t = u.t;
										// Update HTML markup.
										WPLT2.updateHTML( t, u );
									}
								}
							);
							WPLT2.widgets.forEach( function ( t ) {
								if ( t.w === u.w ) {
									t.t = u.t;
									WPLT2.updateHTML( t, u );
								}
							} );
						}
					);
				}
				// Re-trigger update.
				setTimeout( WPLT2.update, WPLT2.pollInterval );
			} catch ( e ) {
				console.warn( 'WP-Liveticker 2 AJAX update failed, stopping automatic updates.' )
			}
		}
	};
	xhr.send( updateReq );
};

/**
 * Do actual update of HTML code.
 *
 * @param t Ticker or Widget reference.
 * @param u Update entity.
 * @return void
 */
WPLT2.updateHTML = function( t, u ) {
	// Prepend HTML of new ticks.
	t.e.innerHTML = u.h + t.e.innerHTML;
	t.e.setAttribute( 'data-wplt2-last', u.t );
	// Remove tail, if limit is set.
	if ( t.l > 0 ) {
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
		// Trigger periodic update of livetickers.
		WPLT2.init();
	}
);
