<?php
	$plugin_dir = realpath( dirname( __FILE__ ) . '/..' );
	$plugin     = $plugin_dir . '/plugin.php';
	$f2d_option = get_option( 'f2d_options', '' );

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
		$attachment = [ ABSPATH . trailingslashit( $f2d_option[ 'path' ] ) . $f2d_option[ 'filename' ] ];
		$headers    = []; 

		add_filter( 'wp_mail_content_type', 'f2d_content_type' );

		wp_mail( 
			$to, 
			'Sweepstakes Entries as of ' . $date, 
			'Attached are the sweepstakes entries as of ' . $date, 
			$headers,
			$attachment
		);
	}

	function f2d_content_type() {
		return 'text/html';
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
