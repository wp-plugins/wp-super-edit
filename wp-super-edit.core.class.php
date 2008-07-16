<?php

if ( !class_exists( 'wp_super_edit_core' ) ) {

    class wp_super_edit_core { 
 
		public $db_options;
		public $db_plugins;
		public $db_buttons;
		public $db_users;
		
		public $core_path;
		public $core_uri;
		
		public $current_user;
 
        function wp_super_edit_core() { // Maintain php4 compatiblity  
        	global $wpdb;

        	$this->db_options = $wpdb->prefix . 'wp_super_edit_options';
        	$this->db_plugins =  $wpdb->prefix . 'wp_super_edit_plugins';
        	$this->db_buttons =  $wpdb->prefix . 'wp_super_edit_buttons';
        	$this->db_users =  $wpdb->prefix . 'wp_super_edit_users';
			$this->core_path = ABSPATH . 'wp-content/plugins/wp-super-edit/';
        	$this->core_uri = get_bloginfo('wpurl') . '/wp-content/plugins/wp-super-edit/';
        	$this->tinymce_plugins_path = $this->core_path . 'tinymce_plugins/';
        	$this->tinymce_plugins_uri = $this->core_uri . 'tinymce_plugins/';
        	$this->is_installed = $this->is_db_installed();
        	
        	$this->management_modes = array(
				'single' => 'One editor setting for all users',
				'roles' => 'Role based editor settings',
				'users' => 'Individual user editor settings'
			);
			
        	if ( !$this->is_installed ) return;
        	
        	$this->management_mode = $this->get_option( 'management_mode' );
        	
        }

        function is_db_installed() {
        	global $wpdb;
        	if( $wpdb->get_var( "SHOW TABLES LIKE '$this->db_options'") == $this->db_options ) return true;
			return false;
        }

        function plugin_init() {
        	global $wpdb;

			$plugins = $wpdb->get_results("
				SELECT name, callbacks FROM $this->db_plugins
				WHERE status = 'yes' and provider = 'wp_super_edit'
			");

			foreach ( $plugins as $number => $plugin ) {
				if ( empty( $plugin->callbacks ) ) unset( $plugins[$number] ) ;
			}
			
			if ( !is_array( $plugins ) || empty( $plugins ) ) return false;
						
			return $plugins;
						
        }

        function get_option( $option_name ) {
        	global $wpdb;
        		
			$option = $wpdb->get_row("
				SELECT value FROM $this->db_options
				WHERE name='$option_name'
			");
		
			$option_value = maybe_unserialize( $option->value );
			
			return $option_value;
        }

        function set_option( $option_name, $option_value ) {
        	global $wpdb;

			$result = $wpdb->get_row("
				SELECT * FROM $this->db_options
				WHERE name='$option_name'
			",ARRAY_N);
			
			$option_value = maybe_serialize( $option_value );
			$option_value = $wpdb->escape( $option_value );
			
			if( count( $result ) == 0 ) {
				$result = $wpdb->query("
					INSERT INTO $this->db_options
					(name, value) 
					VALUES ('$option_name', '$option_value')
				");
				return true;
			} elseif( count( $result ) > 0 ) {
				$result = $wpdb->query("
					UPDATE $this->db_options
					SET value='$option_value'
					WHERE name='$option_name'
					");
				return true;
			}
					
			return false;
        }
        
        function get_user_settings( $user_name = 'wp_super_edit_default' ) {
        	global $wpdb;
        	
			$user_settings = $wpdb->get_results("
				SELECT user_name, editor_options 
				FROM $this->db_users
				WHERE user_name = '$user_name'
			");
			
			$this->current_user['editor_options'] = maybe_unserialize( $user_settings[0]->editor_options );

			for ( $button_rows = 1; $button_rows <= 4; $button_rows += 1) {
				$this->current_user['buttons'][$button_rows] = explode( ',', $this->current_user['editor_options']['theme_advanced_buttons' . $button_rows] );
			}

        }

    }

}

?>