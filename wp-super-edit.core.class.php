<?php

if ( !class_exists( 'wp_super_edit_core' ) ) {

    class wp_super_edit_core { 
 
		var $db_options;
		var $db_plugins;
		var $db_buttons;
		var $db_users;
		
		var $core_path;
		var $core_uri;
		 
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
				WHERE status = 'yes'
			");
			
			if ( !is_array( $plugins ) || empty( $plugins ) ) return false;

			foreach ( $plugins as $number => $plugin ) {
				if ( empty( $plugin->callbacks ) ) unset( $plugins[$number] ) ;
			}
						
			return $plugins;
						
        }

        function check_registered( $type, $name ) {
        	global $wpdb;
 
			$name_col = 'name';
			$role = '';
	
			switch ( $type ) {
				case 'plugin':
					if ( $this->plugins[$name]->name == $name ) return true;
					$db_table = $this->db_plugins;
					break;
				case 'button':
					if ( $this->buttons[$name]->name == $name ) return true;
					$db_table = $this->db_buttons;
					break;
				case 'user':
					$db_table = $this->db_users;
					$name_col = 'user_name';
					switch ( $this->management_mode ) {
						case 'single':
							$role = " AND user_type='single'";
							break;
						case 'roles':
							$role = " AND user_type='roles'";
							break;
						case 'users':
							$role = " AND user_type='users'";
							break;
					}
					break;
				case 'option':
					$db_table = $this->db_options;
			}

			$register_check = $wpdb->get_var("
				SELECT $name_col FROM $db_table
				WHERE $name_col='$name'$role
			");
			
			if ( $register_check == $name) return true;
			
			return false;
			
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
        
        function get_user_settings( $user_name ) {
        	global $wpdb;
 
			switch ( $this->management_mode ) {
				case 'single':
					$role = " AND user_type='single'";
					break;
				case 'roles':
					$role = " AND user_type='roles'";
					break;
				case 'users':
					$role = " AND user_type='users'";
					break;
			}
			
			if ( $user_name == 'wp_super_edit_default' ) $role = " AND user_type='single'";
			
			$user_settings = $wpdb->get_row("
				SELECT user_name, user_nicename, editor_options 
				FROM $this->db_users
				WHERE user_name = '$user_name'$role
			");
						
			return $user_settings;

        }

    }

}

?>