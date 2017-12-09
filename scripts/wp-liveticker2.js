function WPLT2() {

}

/**
 * Initialize WP-Liveticker 2 JS component.
 */
WPLT2.init = function () {
	// Get ticker elements
	WPLT2.ticker = jQuery("ul.wplt2-ticker-ajax").map(function () {
		return {s: jQuery(this).data('wplt2Ticker'), e: this};
	});
	// Extract AJAX settings.
	WPLT2.ajaxURL = ajax_object.ajax_url;
	WPLT2.pollInterval = ajax_object.poll_interval;
	// Trigger update, if necessary.
	if (WPLT2.ticker.length > 0 && WPLT2.pollInterval > 0) {
		setTimeout(WPLT2.update, WPLT2.pollInterval);
	}
};

/**
 * Update liveticker on current page via AJAX call.
 */
WPLT2.update = function () {
	// Extract ticker-slug, limit and timestamp of last poll.
	const updateReq = jQuery.map(WPLT2.ticker, function (e, i) {
		return {s: e.s, l: jQuery(e.e).data('wplt2Limit'), t: jQuery(e.e).data('wplt2Last')};
	});

	// Issue AJAX request.
	jQuery.post(
		WPLT2.ajaxURL,
		{
			'action': 'wplt2_update-ticks',
			'update': updateReq
		},
		function (res) {
			try {
				const update = JSON.parse(res);
				if (update) {
					jQuery.each(update, function (i, u) {
						jQuery.each(WPLT2.ticker, function (j, t) {
							if (t.s === u.s) {
								// Set HTML content.
								jQuery(t.e).html(u.h);
								// Set last poll timestamp.
								jQuery(t.e).data('wplt2Last', u.t);
							}
						});
					});
				}
				// Re-trigger update.
				setTimeout(WPLT2.update, WPLT2.pollInterval);
			} catch (e) {
				console.warn('WP-Liveticker 2 AJAX update failed, stopping automatic updates.')
			}
		}
	);
};

jQuery(document).ready(function ($) {
	// Trigger periodic update of livetickers.
	WPLT2.init();
});
