<?php

if ( !class_exists( 'wp_super_edit_core' ) ) {

    class wp_super_edit_core { 
 
		public $version;
		public $db_options;
		public $db_plugins;
		public $db_buttons;
		public $db_users;
		public $core_path;
		public $core_uri;
 
        function wp_super_edit_core() { // Maintain php4 compatiblity        	
        	$this->version = '2.0';
        	$this->db_options = $wpdb->prefix . 'wp_super_edit_options';
        	$this->db_plugins =  $wpdb->prefix . 'wp_super_edit_plugins';
        	$this->db_buttons =  $wpdb->prefix . 'wp_super_edit_buttons';
        	$this->db_users =  $wpdb->prefix . 'wp_super_edit_users';
			$this->core_path = ABSPATH . 'wp-content/plugins/wp-super-edit/';
        	$this->core_uri = get_bloginfo('wpurl') . '/wp-content/plugins/wp-super-edit/';
        	$this->tinymce_plugins_uri = $this->core_uri . 'tinymce_plugins/';

        }
        
    }
    
    class wp_super_edit_plugin_init extends wp_super_edit_core {

        function plugin_init() {
        	global $wpdb;

			$plugins = $wpdb->get_results("
				SELECT name, callbacks FROM $this->db_plugins
				WHERE status = 'yes'
			");
			
			echo gettype( $plugins );
			
			print_r( $plugins );
			
/*
			
			foreach ($plugins as $plugin) {
				echo $plugin->name;
			}
*/
			
        }


    }


    class wp_super_edit_db extends wp_super_edit_core { 
    
        
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
        
        function add_tinymce_button( $name = '', $nicename = '', $description = '', $provider = '', $plugin = '', $status = '', $separator = '', $row = '', $position = '' ) {
        
			$wpdb->query("
				INSERT INTO $wp_super_edit->db_buttons 
				(name, nicename, description, provider, plugin, status, separator, row, position) 
				VALUES ('$name', '$nicename', '$description', '$provider', '$plugin', '$status', '$separator', $row, $position)
			");
		
		}
        
    }



}

?>