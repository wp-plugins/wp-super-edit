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
        	global $wpdb;

        	$this->version = '2.0';
        	$this->db_options = $wpdb->prefix . 'wp_super_edit_options';
        	$this->db_plugins =  $wpdb->prefix . 'wp_super_edit_plugins';
        	$this->db_buttons =  $wpdb->prefix . 'wp_super_edit_buttons';
        	$this->db_users =  $wpdb->prefix . 'wp_super_edit_users';
			$this->core_path = ABSPATH . 'wp-content/plugins/wp-super-edit/';
        	$this->core_uri = get_bloginfo('wpurl') . '/wp-content/plugins/wp-super-edit/';
        	$this->tinymce_plugins_path = $this->core_path . 'tinymce_plugins/';
        	$this->tinymce_plugins_uri = $this->core_uri . 'tinymce_plugins/';

        }
        
    }
    
    class wp_super_edit_plugin_init extends wp_super_edit_core {

        function plugin_init() {
        	global $wpdb;
/*

	// Plugin Callback functions
	$superedit_plugins = get_option('superedit_plugins');
	
	if ( is_array( $superedit_plugins ) ) {
		foreach ( $superedit_plugins as $name => $plugin ) {
			if ( isset( $plugin['callbacks'] ) && $plugin['callbacks'] != "" && $plugin['status'] == 'Y') {
				$superedit_callbacks = explode(',', $plugin['callbacks']);
				
				$tinymce_plugins_loc = ABSPATH.'wp-content/plugins/wp-super-edit/tinymce_plugins/';
				require( $tinymce_plugins_loc.$name.'/functions.php');
				
				foreach ( $superedit_callbacks as $callback => $command ) {
					call_user_func(trim($command));
				}
				
			}
		}
	}
			$plugins = $wpdb->get_results("
				SELECT name, callbacks FROM $this->db_plugins
				WHERE status = 'yes'
			");
			
			echo gettype( $plugins );
			
			print_r( $plugins );
			

			
			foreach ($plugins as $plugin) {
				echo $plugin->name;
			}
*/
			
        }


    }

    class wp_super_edit_registry extends wp_super_edit_core {

        function get_registered_buttons() {
        	global $wpdb;
        	
			$buttons = $wpdb->get_results("
				SELECT name FROM $this->db_buttons
			");
			
			return $buttons;
        }
        
        function get_registered_plugins() {
        	global $wpdb;
        	
			$plugins = $wpdb->get_results("
				SELECT name FROM $this->db_plugins
			");
			
			return $plugins;
        }
        
        function plugin_folder_scan() {

			$tinymce_plugins_loc = $this->tinymce_plugins_path;
			$tinymce_plugins = @ dir( $this->tinymce_plugins_path );
			while( ( $tinymce_plugin = $tinymce_plugins->read() ) !== false) {
			
				$tinymce_plugin_path = $this->tinymce_plugins_path . '/'. $tinymce_plugin;
				
				if ( is_dir( $tinymce_plugin_path ) && is_readable( $tinymce_plugin_path ) ) {
					if ( $tinymce_plugin{0} == '.' || $tinymce_plugin == '..' ) {
						continue;
					}
					$tinymce_plugin_dir = @ dir( $tinymce_plugin_path );
					while ( ( $tinymce_plugin_config = $tinymce_plugin_dir->read() ) !== false) {
						if ( $tinymce_plugin_config == 'config.php' ) {
						
							include_once( $tinymce_plugin_path . $tinymce_plugin_config );
							
							break;
						}
					}
				}
			}

        }

        function register_tinymce_button( $button = array() ) {
        	global $wpdb;
        	
        	// Check registered
			$register_check = $wpdb->get_row("
				SELECT name FROM $this->db_buttons
				WHERE name='" . $button['name'] ."'
			");
			
			if ( $option->value == $button['name'] ) return true;
			
			$button_values = '"' .
				$button['name'] . '", "' . 
				$button['nicename'] . '", "' . 
				$button['description'] . '", "' . 
				$button['provider'] . '", "' . 
				$button['plugin'] . '", "' . 
				$button['status'] . '", "' . 
				$button['separator'] . '", ' . 
				$button['row'] . ', ' . 
				$button['position']
			;
			
			$wpdb->query("
				INSERT INTO $this->db_buttons 
				(name, nicename, description, provider, plugin, status, button_separator, row, position) 
				VALUES ($button_values)
			");
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
        
        function add_tinymce_button( $button = array() ) {
        	global $wpdb;

			$button_values = '"' .
				$button['name'] . '", "' . 
				$button['nicename'] . '", "' . 
				$button['description'] . '", "' . 
				$button['provider'] . '", "' . 
				$button['plugin'] . '", "' . 
				$button['status'] . '", "' . 
				$button['separator'] . '", ' . 
				$button['row'] . ', ' . 
				$button['position']
			;
			
			$wpdb->query("
				INSERT INTO $this->db_buttons 
				(name, nicename, description, provider, plugin, status, button_separator, row, position) 
				VALUES ($button_values)
			");
		
		}
        
    }



}

?>