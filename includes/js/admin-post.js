jQuery( document ).ready( function( $ ){
	
	var download_id;
	
	// Display download modal
	$( '#sdm-media-button' ).click( function( e ) {
		$( '#sdm-download-modal' ).show();
		
		e.preventDefault();
	} );
	
	// Close download modal
	$( '#sdm-download-modal-close' ).click( function( e ) {
		$( '#sdm-download-modal' ).hide();
		
		e.preventDefault();
	} );
	
	$( '.media-modal-backdrop' ).click( function() {
		$( '#sdm-download-modal' ).hide();
	} );
	
	// Hide/show color select
	if( $( '#sdm-download-style' ).val() == 'button' ) {
		$( '.sdm-download-color-container' ).show();
	}
	else {
		$( '.sdm-download-color-container' ).hide();	
	}
	
	// Hide/show color select on change
	$( '#sdm-download-style' ).change( function() {
		if( $( '#sdm-download-style' ).val() == 'button' ) {
			$( '.sdm-download-color-container' ).slisdmwn();
		}
		else {
			$( '.sdm-download-color-container' ).slideUp();	
		}
	} );
	
	// Selectable list items
	var selectableOpts = {
		selected: function( e, ui ) {
			download_id = $( ui.selected ).attr( 'data-ID' );
			$( '.download-details' ).show();
		}
	};
	
	// Set selectable item
	$( '#selectable_list' ).selectable( selectableOpts );
	
	// Download insert button
	$( '#sdm-download-button' ).click( function() {
		var download_text = $( '#sdm-download-text' ).val();
		var download_style = $( '#sdm-download-style' ).val();
		var download_color = $( '#sdm-download-color' ).val();
		
		// Check if button and add color
		if( download_style == 'button' ) {
			color = ' color="' + download_color + '"'
		}
		else {
			color = ''
		}
		
		// Add to editor
		window.send_to_editor( '[download id=' + download_id + ' text="' + download_text + '" style="' + download_style + '"' + color + ']' );
		
		// Hide modal
		$( '#sdm-download-modal' ).hide();
	} );
	
	// Download filesize button
	$( '#sdm-filesize-button' ).click( function() {
		// Add to editor
		window.send_to_editor( '[download_size id=' + download_id + ']' );
		
		// Hide modal
		$( '#sdm-download-modal' ).hide();
	} );
	
	// Download count button
	$( '#sdm-count-button' ).click( function() {
		// Add to editor
		window.send_to_editor( '[download_count id=' + download_id + ']' );
		
		// Hide modal
		$( '#sdm-download-modal' ).hide();
	} );
               

} );