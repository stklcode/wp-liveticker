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
	WPLT2.widgets = jQuery("ul.wplt2-widget-ajax").map(function () {
		return {s: jQuery(this).data('wplt2Ticker'), e: this};
	});
	// Extract AJAX settings.
	WPLT2.ajaxURL = ajax_object.ajax_url;
	WPLT2.nonce = ajax_object.nonce;
	WPLT2.pollInterval = ajax_object.poll_interval;
	// Trigger update, if necessary.
	if ((WPLT2.ticker.length > 0 || WPLT2.widgets.length) && WPLT2.pollInterval > 0) {
		setTimeout(WPLT2.update, WPLT2.pollInterval);
	}
};

/**
 * Update liveticker on current page via AJAX call.
 */
WPLT2.update = function () {
	// Extract ticker-slug, limit and timestamp of last poll.
	const updateReq = jQuery.merge(
		jQuery.map(WPLT2.ticker, function (e, i) {
			return {s: e.s, l: jQuery(e.e).data('wplt2Limit'), t: jQuery(e.e).data('wplt2Last')};
		}),
		jQuery.map(WPLT2.widgets, function (e, i) {
			return {w: e.s, l: jQuery(e.e).data('wplt2Limit'), t: jQuery(e.e).data('wplt2Last')};
		})
	);

	// Issue AJAX request.
	jQuery.post(
		WPLT2.ajaxURL,
		{
			'action'     : 'wplt2_update-ticks',
			'_ajax_nonce': WPLT2.nonce,
			'update'     : updateReq
		},
		function (res) {
			try {
				const update = JSON.parse(res);
				if (update) {
					jQuery.each(update, function (i, u) {
						jQuery.each(WPLT2.ticker, function (j, t) {
							if (t.s === u.s) {
								WPLT2.updateHTML(t.e, u.h, u.t);
							}
						});
						jQuery.each(WPLT2.widgets, function (j, t) {
							if (t.s === u.w) {
								WPLT2.updateHTML(t.e, u.h, u.t);
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

/**
 * Do actual update of HTML code.
 *
 * @param e The element.
 * @param h The new HTML code.
 * @param t Timestamp of update.
 */
WPLT2.updateHTML = function(e, h, t) {
	// Prepend HTML of new ticks.
	jQuery(e).prepend(h);
	// Remove tail, if limit is set.
	const l = jQuery(e).data('wplt2Limit');
	if (l > 0) {
		jQuery(e).find('li').slice(l).remove();
	}
	// Update last poll timestamp.
	jQuery(e).data('wplt2Last', t);
};

jQuery(document).ready(function ($) {
	// Trigger periodic update of livetickers.
	WPLT2.init();
});
