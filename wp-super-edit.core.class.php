<?php

if ( !class_exists( 'wp_super_edit_core' ) ) {

    class wp_super_edit_core { 
 
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
        	
        	$this->ui = false;
        	
        	$this->management_modes = array(
				'single' => 'One editor setting for all users',
				'roles' => 'Role based editor settings',
				'users' => 'Individual user editor settings'
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
			
			$button_query = "
				SELECT name, provider, plugin, status FROM $this->db_buttons
			";
			
			$plugin_query = "
				SELECT name, url, status, callbacks FROM $this->db_plugins
			";
			
			if ( $this->ui == 'plugins' ) {
				$plugin_query = "
					SELECT name, nicename, description, provider, status 
					FROM $this->db_plugins
				";
			}
			
			if ( $this->ui == 'buttons' ) {
				$button_query = "
					SELECT name, nicename, description, provider, status 
					FROM $this->db_buttons
				";
			}
        	
			$buttons = $wpdb->get_results( $button_query );
			
			foreach( $buttons as $button ) {
				$this->buttons[$button->name] = $button;
				if ( $button->status == 'yes' ) {
					$this->active_buttons[$button->name] = $button;
				}
			}
			
			$plugin_result = $wpdb->get_results( $plugin_query );
						
			foreach ( $plugin_result as $plugin ) {
				$this->plugins[$plugin->name] = $plugin;
			}
				
        }

        function is_db_installed() {
        	global $wpdb;
        	if( $wpdb->get_var( "SHOW TABLES LIKE '$this->db_options'") == $this->db_options ) return true;
			return false;
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
 
         function tinymce_settings( $initArray ) {
        	global $current_user;
						
			switch ( $this->management_mode ) {
				case 'single':
					$user_settings = $this->get_user_settings( 'wp_super_edit_default' );
					break;
				case 'roles':
					$user_roles = array_keys( $current_user->caps );
					$user_settings = $this->get_user_settings( $user_roles[0] );
					break;
				case 'users':
					$user_settings = $this->get_user_settings( $current_user->user_login );
					break;
			}
			
			$tinymce_user_settings = maybe_unserialize( $user_settings->editor_options );
			
			for ( $button_row = 1; $button_row <= 4; $button_row += 1) {
			
				$row_name = 'theme_advanced_buttons' . $button_row;
				$initArray[$row_name] = $tinymce_user_settings[$row_name];
			
			}

			return $initArray;

        }
        

    }

}

?>