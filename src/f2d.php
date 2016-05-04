<?php
	add_action( 'wp_ajax_form_to_disk', 'form_to_disk' );
	add_action( 'wp_ajax_nopriv_form_to_disk', 'form_to_disk' );

	$f2d_option = get_option( 'f2d_options', '' );

	function form_to_disk() {

		global $f2d_option;
		date_default_timezone_set( 'America/Los_Angeles' );

		if ( isset( $_POST[ 'marker' ] ) && 'sweepstakes' == $_POST[ 'marker' ] ) {
			$protected_dir = ABSPATH . trailingslashit( $f2d_option[ 'path' ] );
			$form_data	   = trim( $_POST[ 'firstName' ] ) . ',';
			$form_data    .= trim( $_POST[ 'lastName' ] ) . ',';
			$form_data    .= trim( $_POST[ 'email' ] ) . ',';
			$form_data    .= trim( $_POST[ 'pageId' ] );
			$entries       = fopen( $protected_dir . $f2d_option[ 'filename' ], 'a' ) 
				or die( 'Unable to process that request' );
			$txt           = date( 'Ymd' );
			$txt          .= ',';
			$txt          .= $form_data;
			fwrite( $entries, $txt . PHP_EOL );
			fclose( $entries );
			die();
		}
	}
