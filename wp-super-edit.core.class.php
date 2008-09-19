<?php

if ( !class_exists( 'wp_super_edit_core' ) ) {

/**
* WP Super Edit Core Class
*
* This class sets up core functions and variables for WP Super Edit. 
* @package wp-super-edit
* @subpackage wp-super-edit-core-class
*/
    class wp_super_edit_core { 
 
		/**
		* Initialize private variables. Set for php4 compatiblity. 
		*/		
		var $db_options;
		var $db_plugins;
		var $db_buttons;
		var $db_users;
		
		var $core_path;
		var $core_uri;

		var $tinymce_plugins_path;
		var $tinymce_plugins_uri;
		
		var $management_modes;
		var $management_mode;
		
		var $plugins;
		var $buttons;
		var $active_buttons;
		
		var $ui;
		var $ui_url;
		var $ui_form_url;
		
		var $nonce;		
		
		var $user_profile;
		
		var $is_tinymce;
		var $js_cache_count;
		
		/**
		* Constructor initializes private variables. Set for php4 compatiblity. 
		*/	
        function wp_super_edit_core() { // Maintain php4 compatiblity  
        	global $wpdb;

        	$this->db_options = $wpdb->prefix . 'wp_super_edit_options';
        	$this->db_plugins =  $wpdb->prefix . 'wp_super_edit_plugins';
        	$this->db_buttons =  $wpdb->prefix . 'wp_super_edit_buttons';
        	$this->db_users =  $wpdb->prefix . 'wp_super_edit_users';
        	
			$this->core_path = WP_PLUGIN_DIR . '/wp-super-edit/';
        	$this->core_uri = WP_PLUGIN_URL . '/wp-super-edit/';
        	$this->tinymce_plugins_path = $this->core_path . 'tinymce_plugins/';
        	$this->tinymce_plugins_uri = $this->core_uri . 'tinymce_plugins/';
        	
        	$this->is_installed = $this->is_db_installed();
        	
        	$this->ui = false;
        	$this->user_profile = false;
        	
        	$this->management_modes = array(
				'single' => __('One editor setting for all users'),
				'roles' => __('Role based editor settings'),
				'users' => __('Individual user editor settings')
			);		

        	if ( strpos( $_SERVER['SCRIPT_FILENAME'], 'tiny_mce_config.php' ) == false ) {
        		$this->is_tinymce = false;
        	} else {
        		$this->is_tinymce = true;
        	}        	
        	
        	if ( is_admin() ) {
				$this->ui = ( !$_REQUEST['wp_super_edit_ui'] ? 'options' : $_REQUEST['wp_super_edit_ui'] );			
				if ( !$this->is_installed ) $this->ui = 'options';
				
				if ( strstr( $_SERVER['PHP_SELF'], 'users.php' ) != false || strstr( $_SERVER['PHP_SELF'], 'profile.php' ) != false ) {
					$this->user_profile = true;
					$this->ui = 'buttons';
				}
				
				$this->ui_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'];
				$this->ui_form_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'] . '&wp_super_edit_ui=' . $this->ui;
				$this->nonce = 'wp-super-edit-update-key';
			}
			
        	if ( !$this->is_installed ) return;
        	
        	$this->management_mode = $this->get_option( 'management_mode' );	
			
			$plugin_query = "
				SELECT name, url, status, callbacks FROM $this->db_plugins
			";
			
			if ( $this->ui == 'plugins' ) {
				$plugin_query = "
					SELECT name, nicename, description, provider, status 
					FROM $this->db_plugins
				";
			}

			$plugin_result = $wpdb->get_results( $plugin_query );
						
			foreach ( $plugin_result as $plugin ) {
				$this->plugins[$plugin->name] = $plugin;
			}
			
			if ( !$this->is_tinymce ) return;

			if ( $this->ui == 'buttons' ) {
				$button_query = "
					SELECT name, nicename, description, provider, status 
					FROM $this->db_buttons
				";
			}

			$button_query = "
				SELECT name, provider, plugin, status FROM $this->db_buttons
			";
			
			$buttons = $wpdb->get_results( $button_query );
			
			foreach( $buttons as $button ) {
				$this->buttons[$button->name] = $button;
				if ( $button->status == 'yes' ) {
					$this->active_buttons[$button->name] = $button;
				}
			}
			
			$this->js_cache_count = 1 + $wpdb->get_var( $wpdb->prepare ( "
				SELECT COUNT(*) FROM $this->db_users WHERE user_type = %s", 
				$this->management_mode
			) );
				
        }
        
		/**
		* Check if database tables are installed. 
		* @return boolean
		*/	
        function is_db_installed() {
        	global $wpdb;
        	if( $wpdb->get_var( "SHOW TABLES LIKE '$this->db_options'") == $this->db_options ) return true;
			return false;
        }

		/**
		* Check if user, plugin, or button is registered in database based on type. 
		* @param string	$type 
		* @param string $name
		* @return boolean
		*/
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
		
		/**
		* Get WP Super Edit option from options database table. 
		* @param string $name
		* @return mixed
		*/
        function get_option( $option_name ) {
        	global $wpdb;
        		
			$option = $wpdb->get_row("
				SELECT value FROM $this->db_options
				WHERE name='$option_name'
			");
		
			$option_value = maybe_unserialize( $option->value );
			
			return $option_value;
        }

		/**
		* Set WP Super Edit option in options database table. 
		* @param string $option_name
		* @param mixed $option_value
		* @return boolean
		*/
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

		/**
		* Get user settings from users database table. 
		* @param string $user_name
		* @return object
		*/        
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
 
 		/**
		* Filter to set up WordPress TinyMCE settings from stored settings based on mode. Check for unregistered
		* buttons and deactivated plugins.
		* @param array $initArray
		* @return array
		*/ 
		function tinymce_settings( $initArray ) {
			global $current_user;
									
			switch ( $this->management_mode ) {
				case 'single':
					$user = 'wp_super_edit_default';
					break;
				case 'roles':
					$user_roles = array_keys( $current_user->caps );
					$user = $user_roles[0];
					break;
				case 'users':
					$user = $current_user->user_login;
					break;
			}
			
			if ( !$this->check_registered( 'users', $user ) ) $user = 'wp_super_edit_default';
			$user_settings = $this->get_user_settings( $user );
			
			$tinymce_user_settings = maybe_unserialize( $user_settings->editor_options );
			
			$button_check = array_keys( $this->buttons );
						
			for ( $button_row = 1; $button_row <= 4; $button_row += 1) {
				
				$row_name = 'theme_advanced_buttons' . $button_row;
			
				$wp_super_edit_check = explode( ',', $tinymce_user_settings[$row_name] );
				$row_check = explode( ',', $initArray[$row_name] );
				
				$wp_super_edit_row_buttons = array();
				$wp_super_edit_row = '';
				$comma_insert = '';
				$unregistered_buttons = array();
				$unregistered = '';
			
				foreach( $wp_super_edit_check as $button_name ) {
					if ( $button_name == '|' ) {
						$wp_super_edit_row_buttons[] = '|';
						continue;
					}
					$plugin = $this->buttons[$button_name]->plugin;
					if ( !empty( $plugin ) ) {
						if ( $this->plugins[$plugin]->status != 'yes' ) continue;
					}
					$wp_super_edit_row_buttons[] = $button_name;
				}
				
				foreach( $row_check as $button_name ) {
					if ( $button_name == '|' ||  $button_name == '') continue;
					if ( !in_array( $button_name, $button_check ) ) $unregistered_buttons[] = $button_name;
				}
				if ( !empty( $wp_super_edit_row_buttons ) ) {
					$wp_super_edit_row = implode( ',', $wp_super_edit_row_buttons );
					$comma_insert = ',';
				}
				if ( !empty( $unregistered_buttons ) ) $unregistered = $comma_insert . implode( ',', $unregistered_buttons );
			
								
				$initArray[$row_name] = $wp_super_edit_row . $unregistered;
			
			}
			
			if ( $this->management_mode != 'single' ) $initArray['old_cache_max'] = $this->js_cache_count;
			
			return $initArray;
		
		}

    }

}

?>
