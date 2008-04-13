<?php

if ( !class_exists( 'wp_super_edit_core' ) ) {

    class wp_super_edit_core { 
 
		public $db_options;
		public $db_plugins;
		public $db_buttons;
		public $db_users;
		public $core_path;
		public $core_uri;
 
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
        	
        	$this->is_db_installed = $this->is_db_installed();
        	
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
        
    }


}

?>