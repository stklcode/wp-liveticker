jQuery(document).ready(function ($) {
	// Trigger periodic update of livetickers.
	setTimeout(wplt2_update_ticker, ajax_object.poll_interval);
});

function wplt2_update_ticker() {
	// Get ticker to update.
	const ticker = jQuery("ul.wplt2-ticker-ajax");
	if (ticker.length > 0) {
		setTimeout(wplt2_update_ticker, ajax_object.poll_interval);
		// Extract ticker-slug, limit and timestamp of last poll.
		const updateReq = jQuery.map(ticker, function (e, i) {
			return {s: jQuery(e).data('wplt2Ticker'), l: jQuery(e).data('wplt2Limit'), t: jQuery(e).data('wplt2Last')};
		});

		// Issue AJAX request.
		jQuery.post(
			ajax_object.ajax_url,
			{
				'action': 'wplt2_update-ticks',
				'update': updateReq
			},
			function (res) {
				// TODO: Update markup.
				setTimeout(wplt2_update_ticker, ajax_object.poll_interval);
			}
		);
	}
}
