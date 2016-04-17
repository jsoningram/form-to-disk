<?php
	class FormToDiskSettings
	{
		/**
		 * Holds the values to be used in the fields callbacks
		 */
		private $options;

		/**
		 * Start up
		 */
		public function __construct()
		{
			add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
			add_action( 'admin_init', [ $this, 'page_init' ] );
		}

		/**
		 * Add options page
		 */
		public function add_plugin_page()
		{
			// This page will be under "Settings"
			add_options_page(
				'F2D Settings', 
				'F2D Settings', 
				'manage_options', 
				'f2d-settings-admin', 
				[ $this, 'create_admin_page' ] 
			);
		}

		/**
		 * Options page callback
		 */
		public function create_admin_page()
		{
			// Set class property
			$this->options = get_option( 'f2d_options' );
			?>
			<div class="wrap">
				<h2>F2D Settings</h2>           
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'f2d_options_group' );   
					do_settings_sections( 'f2d-settings-admin' );
					submit_button(); 
				?>
				</form>
			</div>
			<?php
		}

		/**
		 * Register and add settings
		 */
		public function page_init()
		{        
			register_setting(
				'f2d_options_group',
				'f2d_options',
				[ $this, 'sanitize' ]
			);

			add_settings_section(
				'customize_f2d',
				'Form to Disk Settings',
				[ $this, 'print_section_info' ],
				'f2d-settings-admin'
			);  

			add_settings_field(
				'email', 
				'Send To:',
				[ $this, 'email_callback' ],
				'f2d-settings-admin',
				'customize_f2d'
			);      

			add_settings_field(
				'path', 
				'Path to File:',
				[ $this, 'path_callback' ],
				'f2d-settings-admin',
				'customize_f2d'
			);      

			add_settings_field(
				'filename', 
				'Name of File:',
				[ $this, 'filename_callback' ],
				'f2d-settings-admin',
				'customize_f2d'
			);      

			add_settings_field(
				'cron', 
				'Reccurance:',
				[ $this, 'cron_callback' ],
				'f2d-settings-admin',
				'customize_f2d'
			);      
		}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public function sanitize( $input )
		{
			$new_input = [];
			if( isset( $input[ 'email' ] ) )
				$new_input[ 'email' ] = sanitize_email( $input[ 'email' ] );

			if( isset( $input[ 'path' ] ) )
				$new_input[ 'path' ] = sanitize_option( 'upload_path', $input[ 'path' ] );

			if( isset( $input[ 'filename' ] ) )
				$new_input[ 'filename' ] = sanitize_file_name( $input[ 'filename' ] );

			if( isset( $input[ 'cron' ] ) )
				$new_input[ 'cron' ] = sanitize_text_field( $input[ 'cron' ] );

			return $new_input;
		}

		/** 
		 * Print the Section text
		 */
		public function print_section_info()
		{
			print 'Enter your settings below:';
		}

		/** 
		 * Get the settings option array and print one of its values
		 */
		public function email_callback()
		{
			printf(
				'<input 
					placeholder="you@domain.com" 
					type="text" 
					id="email" 
					name="f2d_options[email]" 
					size="50"
					value="%s" />',
				isset( $this->options[ 'email' ] ) ? esc_attr( $this->options[ 'email' ]) : ''
			);
		}

		public function path_callback()
		{
			printf(
				'<input 
					placeholder="Relative to ' . ABSPATH . '"
					type="text" 
					id="path" 
					name="f2d_options[path]" 
					size="50"
					value="%s" />',
				isset( $this->options[ 'path' ] ) ? esc_attr( $this->options[ 'path' ]) : ''
			);
		}
		
		public function filename_callback()
		{
			printf(
				'<input 
					type="text" 
					id="filename" 
					name="f2d_options[filename]" 
					size="50"
					value="%s" />',
				isset( $this->options[ 'filename' ] ) ? esc_attr( $this->options[ 'filename' ]) : ''
			);
		}

		public function cron_callback()
		{
			printf(
				'<input 
					placeholder="hourly, twicedaily, daily, weekly"
					type="text" 
					id="cron" 
					name="f2d_options[cron]" 
					size="50"
					value="%s" />',
				isset( $this->options[ 'cron' ] ) ? esc_attr( $this->options[ 'cron' ]) : ''
			);
		}
	}

	if ( is_admin() )
		$f2d_settings = new FormToDiskSettings();
