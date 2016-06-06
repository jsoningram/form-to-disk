<?php
	$plugin_dir = realpath( dirname( __FILE__ ) . '/..' );
	$plugin     = $plugin_dir . '/plugin.php';
	$f2d_option = get_option( 'f2d_options', '' );

	add_action( 'admin_menu', 'f2d_mkdir' );

	function f2d_mkdir() {
		global $f2d_option;
		$path = trailingslashit( $f2d_option[ 'path' ] );

		if ( isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] ) {
			if ( ! is_dir( $path ) ) {
				mkdir( ABSPATH . $path, 0755 );
			}
		}
	}

	register_activation_hook( $plugin, 'f2d_activation' );

	function f2d_activation() {
		global $f2d_option;
		if ( ! wp_next_scheduled( 'send_f2d' ) ) {
		  wp_schedule_event( time(), $f2d_option[ 'cron' ], 'send_f2d' );
		}

	}

	add_action( 'send_f2d', 'send_sweepstakes_entries' );

	function send_sweepstakes_entries() {
		global $f2d_option;

		date_default_timezone_set( 'America/Los_Angeles' );

		$to         = $f2d_option[ 'email' ];
		$date       = date( 'Y-m-d' );
		$headers    = []; 
		$dir        = ABSPATH . trailingslashit( $f2d_option[ 'path' ] );
		$filename   = $f2d_option[ 'filename' ];
		$out        = $dir . $date . '/' . $date . '-' . $filename;

		if ( move_files( $dir, $date ) ) {
			concat_files( $dir, $filename, $date, $out );
			$attachment = [ $out ];
		} else {
			$attachment = [ $dir . $filename ];
		}

		add_filter( 'wp_mail_content_type', 'f2d_content_type' );

		wp_mail( 
			$to, 
			'Sweepstakes Entries for week ending ' . $date, 
			'Attached are the sweepstakes entries for the week ending ' . $date, 
			$headers,
			$attachment
		);
	}

	function f2d_content_type() {
		return 'text/html';
	}

	function move_files( $dir, $date ) {
		$files     = glob( $dir . '*.csv' );
		$num_files = count( $files );

		if ( 1 < $num_files ) {
			$new_dir = $dir . $date;
			mkdir( $new_dir, 0755 );

			foreach ( $files as $key => $file ) {
				rename( $file, $new_dir . '/' . basename( $file ) );
			}
			return true;
		} else {
			return false;
		}
	}

	function concat_files( $dir, $filename, $date, $out ) {
		$files = glob( $dir . $date . '/*.csv' );
		foreach ( $files as $file ) {
			if ( file_put_contents( $out, file_get_contents( $file ),  FILE_APPEND ) ) {
				unlink( $file );
			}
		}
	}
	
	register_deactivation_hook( $plugin, 'f2d_deactivation' );

	function f2d_deactivation() {
			wp_clear_scheduled_hook( 'send_f2d' );
	}

	add_filter( 'cron_schedules', 'ten_weekly_reccurence' ); 

	function ten_weekly_reccurence() {
		return [ 
			'weekly' => [ 'interval' => 604800, 'display' => 'Once Weekly' ]
		];
	}
