<?php
	add_action( 'wp_ajax_form_to_disk', 'form_to_disk' );
	add_action( 'wp_ajax_nopriv_form_to_disk', 'form_to_disk' );

	$f2d_option = get_option( 'f2d_options', '' );

	function form_to_disk() {

		global $f2d_option;
		date_default_timezone_set( 'America/Los_Angeles' );

		if ( isset( $_POST[ 'marker' ] ) && 'sweepstakes' == $_POST[ 'marker' ] ) {
			$dir = ABSPATH . trailingslashit( $f2d_option[ 'path' ] );
			$form_data	   = trim( $_POST[ 'firstName' ] ) . ',';
			$form_data    .= trim( $_POST[ 'lastName' ] ) . ',';
			$form_data    .= trim( $_POST[ 'email' ] ) . ',';
			$form_data    .= trim( $_POST[ 'pageId' ] );
			$file          = $f2d_option[ 'filename' ];
			$file_size     = filesize( $dir . $file );

			if ( $file_size >= 1000000 ) {
				rename( 
					$dir . $file, 
					$dir . 'part-' . date( 'Ymd.is' ) . '-' . $file 
				);
			}

			$entries       = fopen( $dir . $file, 'a' ) 
				or die( 'Unable to open the file' );
			$txt           = date( 'Ymd' );
			$txt          .= ',';
			$txt          .= $form_data;
			fwrite( $entries, $txt . PHP_EOL );
			fclose( $entries );

			echo json_encode( $_POST );
			die();
		}
	}
