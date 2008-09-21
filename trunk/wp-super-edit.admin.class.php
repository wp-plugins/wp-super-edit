<?php

if ( class_exists( 'wp_super_edit_core' ) ) {

/**
* WP Super Edit Admin Class
*
* This class uses WP Super Edit core class and variables and extends by creating user interfaces
* and administrative functions for WP Super Edit. 
* @package wp-super-edit
* @subpackage wp-super-edit-admin-class
*/    
    class wp_super_edit_admin extends wp_super_edit_core {
		
		/**
		* Gets user configuration data from user database tables and returns a complex settings 
		* array. User checked for management mode changes.
		* @param string $user_name
		* @return array
		*/
        function get_user_settings_ui( $user_name ) {
        	global $wpdb, $userdata;
        	
			if ( !$this->check_registered( 'user', $user_name ) ) $user_name = 'wp_super_edit_default';
			
			$user_settings = $this->get_user_settings( $user_name );
			
			$current_user['user_name'] = $user_name;
			$current_user['user_nicename'] = $user_settings->user_nicename;
			
			if ( $this->management_mode == 'users' && $this->user_profile == true ) {
				$current_user['user_nicename'] = $userdata->display_name;
			} 
						
			$current_user['editor_options'] = maybe_unserialize( $user_settings->editor_options );

			for ( $button_rows = 1; $button_rows <= 4; $button_rows += 1) {
				
				if ( $current_user['editor_options']['theme_advanced_buttons' . $button_rows] == '' ) {
					$current_user['buttons'][$button_rows] = array();
					continue;
				}
				
				$current_user['buttons'][$button_rows] = explode( ',', $current_user['editor_options']['theme_advanced_buttons' . $button_rows] );
			}
			
			return $current_user;

        }

		/**
		* Removes database tables for uninstallation. 
		*/
		function uninstall() {
			global $wpdb;
			
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS ' . $this->db_options ));
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS ' . $this->db_plugins ));
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS ' . $this->db_buttons ));
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS ' . $this->db_users ));
			
			delete_option( 'wp_super_edit_tinymce_scan' );
			
			$this->is_installed = false;

			// $url = add_query_arg( '_wpnonce', wp_create_nonce( 'deactivate-plugin_wp-super-edit/wp-super-edit.php' ), get_bloginfo('wpurl') . '/wp-admin/plugins.php?action=deactivate&plugin=wp-super-edit/wp-super-edit.php' );
			// wp_redirect( $url );

		}

		/**
		* Set WP Super Edit options from Options administrative interface.
		*/
		function do_options() {
			global $wpdb;
			
			$this->set_option( 'management_mode', $_REQUEST['wp_super_edit_management_mode'] );
			$this->management_mode = $this->get_option( 'management_mode' );

			if ( $_REQUEST['wp_super_edit_reset_default_user'] == 'reset_default_user' ) {
				$tiny_mce_scan = $this->get_option( 'tinymce_scan' );
				$this->update_user_settings( 'wp_super_edit_default', $tiny_mce_scan );
			}
			
			if ( $_REQUEST['wp_super_edit_reset_users'] == 'reset_users' ) {

				$user_settings = $this->get_user_settings( 'wp_super_edit_default' );
						
				$wpdb->query( $wpdb->prepare( "
					UPDATE $this->db_users
					SET editor_options = %s
					WHERE user_name != 'wp_super_edit_default'", 
					$user_settings->editor_options
				) );

			}
			
			if ( $_REQUEST['wp_super_edit_rescan_plugins'] == 'rescan_plugins' ) {
				wp_super_edit_plugin_folder_scan();
			}			
		}
		
		/**
		* Activate and deactivate WP Super Edit TinyMCE plugins from Plugins administrative interface.
		*/
		function do_plugins() {
			global $wpdb;
			
			foreach ( $this->plugins as $plugin ) {
				if ( $_REQUEST['wp_super_edit_plugins'][$plugin->name] == 'yes' ) {
					$status = 'yes';
				} else {
					$status = 'no';
				}
				
				$plugin->status = $status;
				
				$this->plugins[$plugin->name] = $plugin;
				
				$plugin_result = $wpdb->query( $wpdb->prepare( "
					UPDATE $this->db_plugins
					SET status=%s
					WHERE name=%s ",
					$status, $plugin->name 
				) );
				$button_result = $wpdb->query( $wpdb->prepare( "
					UPDATE $this->db_buttons
					SET status=%s
					WHERE plugin=%s ",
					$status, $plugin->name 
				) );
			}
									
		}
		
		/**
		* Set button settings from Editor Buttons administrative interface.
		*/
		function do_buttons() {
									
			if ( $_REQUEST['wp_super_edit_action_control'] == 'reset_default' ) {
				$user = 'wp_super_edit_default';
			} else {
				$user = $_REQUEST['wp_super_edit_user'];
			}

			$current_settings = $this->get_user_settings_ui( $user );
			$current_user_settings = $current_settings['editor_options'];
			unset( $current_settings );
			
			if ( $_REQUEST['wp_super_edit_action_control'] == 'update' || $_REQUEST['wp_super_edit_action_control'] == 'set_default' ) {
				
				$separators = explode( ',', $_REQUEST['wp_super_edit_separators'] );
				
				$wp_super_edit_rows[1] = explode( ',', $_REQUEST['wp_super_edit_row_1'] );
				$wp_super_edit_rows[2] = explode( ',', $_REQUEST['wp_super_edit_row_2'] );
				$wp_super_edit_rows[3] = explode( ',', $_REQUEST['wp_super_edit_row_3'] );
				$wp_super_edit_rows[4] = explode( ',', $_REQUEST['wp_super_edit_row_4'] );
	
				foreach( $wp_super_edit_rows as $wp_super_edit_row_number => $wp_super_edit_row ) {
					if ( empty( $wp_super_edit_row ) ) continue;
						
					$button_row_setting = array();
					$button_row = '';
					
					foreach( $wp_super_edit_row as $wp_super_edit_button ) {
					
						if ( empty( $wp_super_edit_button ) ) continue;
						
						$button_row_setting[] = $wp_super_edit_button;
						
						if ( in_array( $wp_super_edit_button, $separators ) ) {
							$button_row_setting[] = '|';
						}
					
					}
									
					$button_row = implode( ',', $button_row_setting );
					$button_array_key = 'theme_advanced_buttons' . $wp_super_edit_row_number;
					
					$current_user_settings[$button_array_key] = $button_row;
					
				}
			} 
			
			$this->update_user_settings( $_REQUEST['wp_super_edit_user'], $current_user_settings );

			if ( $_REQUEST['wp_super_edit_action_control'] == 'set_default' && !$this->user_profile ) {
				$this->update_user_settings( 'wp_super_edit_default', $current_user_settings );
			}
			
		}

		/**
		* Register WP Super Edit TinyMCE plugin in plugin database table. 
		* @param array $plugin 
		*/
        function register_tinymce_plugin( $plugin = array() ) {
        	global $wpdb;
			
			if ( $this->check_registered( 'plugin', $plugin['name'] ) ) return true;
			
			$wpdb->query( $wpdb->prepare( "
				INSERT INTO $this->db_plugins
				( name, nicename, description, provider, status, callbacks ) 
				VALUES ( %s, %s, %s, %s, %s, %s )", 
				$plugin['name'], $plugin['nicename'], $plugin['description'], $plugin['provider'], $plugin['status'], $plugin['callbacks']
			) );
        	
        }
        
		/**
		* Register WP Super Edit TinyMCE button in button database table. 
		* @param array $button 
		*/
        function register_tinymce_button( $button = array() ) {
        	global $wpdb;
			
			if ( $this->check_registered( 'button', $button['name'] ) ) return true;

			$wpdb->query( $wpdb->prepare( "
				INSERT INTO $this->db_buttons 
				( name, nicename, description, provider, plugin, status )  
				VALUES ( %s, %s, %s, %s, %s, %s )", 
				$button['name'], $button['nicename'], $button['description'], $button['provider'], $button['plugin'], $button['status'] 
			) );

		}

		/**
		* Register WP Super Edit user settings in users database table. 
		* @param string $user_name 
		* @param string $user_nicename 
		* @param array $user_settings 
		* @param string $type 
		*/
        function register_user_settings( $user_name = 'wp_super_edit_default', $user_nicename = 'Default Editor Settings', $user_settings, $type = 'single' ) {
        	global $wpdb;
			
			if ( $this->check_registered( 'user', $user_name ) ) return;
			
			$settings = maybe_serialize( $user_settings );

			$wpdb->query( $wpdb->prepare( "
				INSERT INTO $this->db_users
				( user_name, user_nicename, user_type, editor_options ) 
				VALUES ( %s, %s, %s, %s )", 
				$user_name, $user_nicename, $type, $settings 
			) );
					
		}

		/**
		* Update WP Super Edit user settings in users database table. 
		* @param string $user_name 
		* @param array $user_settings 
		*/
        function update_user_settings(  $user_name = 'wp_super_edit_default', $user_settings ) {
        	global $wpdb;
			
			$settings = maybe_serialize( $user_settings );
			
			$management_mode = ( $user_name == 'wp_super_edit_default' ? 'single' : $this->management_mode );
						
			$wpdb->query( $wpdb->prepare( "
				UPDATE $this->db_users
				SET editor_options = %s 
				WHERE user_name = %s AND user_type = %s LIMIT 1", 
				$settings, $user_name, $management_mode 
			) );
					
		}

		/**
		* Register new user settings in users database table based on management mode.
		* @param string $user_name 
		*/
		function register_new_user( $user_name ) {
        	global $wpdb, $wp_roles, $userdata;

        	switch ( $this->management_mode ) {
				case 'single':
					return;
				case 'roles':
					if ( isset( $wp_roles->role_names[$user_name] ) ) {
						if ( $this->check_registered( 'user', $user_name ) ) return;
						$nice_name = translate_with_context( $wp_roles->role_names[$user_name] );
						$user_settings = $this->get_user_settings( 'wp_super_edit_default' );
						$editor_options = maybe_unserialize( $user_settings->editor_options );
						$this->register_user_settings( $user_name, $nice_name, $editor_options, $this->management_mode );
					}
					break;
				case 'users':
					if ( $this->check_registered( 'user', $user_name ) ) return;
					$user_settings = $this->get_user_settings( 'wp_super_edit_default' );
					$editor_options = maybe_unserialize( $user_settings->editor_options );
					$this->register_user_settings( $userdata->user_login, 'user', $editor_options, $this->management_mode );
					break;	
				default:
					break;
			}
		
		}


		/**
		* Display or return html tag with attributes.
		* @param array $html_options options and content to display
		* @return mixed
		*/
		function html_tag( $html_options = array() ) {

			$attributes = '';
			$composite = '';
			
			foreach ( $html_options as $name => $option ) {
				if ( $name == 'tag' ) continue;
				if ( $name == 'content' ) continue;
				if ( $name == 'return' ) continue;
				if ( $name == 'tag_type' ) continue;
				$html_attributes .= sprintf( ' %s="%s"', $name, $option );
			}
			
			switch ( $html_options['tag_type'] ) {
				case 'single':
					$format = '%3$s <%1$s%2$s />' ;
					break;
				case 'single-after':
					$format = '<%1$s%2$s /> %3$s' ;
					break;
				case 'open':
					$format = '<%1$s%2$s>%3$s';
					break;
				case 'close':
					$format = '%3$s</%1$s>';
					break;
				default:
					$format = '<%1$s%2$s>%3$s</%1$s>';
					break;
			}
				
			$composite = sprintf( $format, $html_options['tag'], $html_attributes, $html_options['content'] );
			
			if ( $html_options['return'] == true ) return $composite ;
			
			echo $composite;
		}

		/**
		* WP Super Edit admin nonce field generator for form security
		* @param string $action nonce action to make keys
		* @return string
		*/		
		function nonce_field( $action = -1 ) { 
			return wp_nonce_field( $action, "_wpnonce", true , false );
		}
		
		/**
		* Administration interface display header and information
		*/
		function ui_header() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'class' => 'wrap',
			) );
			
			$this->html_tag( array(
				'tag' => 'h2',
				'content' => __('WP Super Edit'),
			) );

			$this->html_tag( array(
				'tag' => 'p',
				'id' => 'wp_super_edit_info',
				'content' => __('To give you more control over the Wordpress TinyMCE WYSIWYG Visual Editor. For more information, visit the <a href="http://factory.funroe.net/projects/wp-super-edit/">WP Super Edit project.</a> You can help continue development by making a <a href="http://factory.funroe.net/contribute/">donation or other contribution</a>.'),
			) );
		}

		/**
		* WP Super Edit administration interface footer
		*/
		function ui_footer() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close',
			) );
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_null',
			) );
		}
		
		/**
		* Start WP Super Edit administration form
		* @param string $action
		* @param string $content
		* @param boolean $return
		* @param string $onsubmit
		* @return mixed
		*/
		function form( $action = '', $content = '', $return = false, $onsubmit = '' ) {
			
			$form_contents = $this->nonce_field('wp_super_edit_nonce-' . $this->nonce);
			
			$form_contents .= $this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single',
				'type' => 'hidden',
				'name' => 'wp_super_edit_action',
				'value' => $action,
				'return' => true
			) );
			
			$form_contents .= $content;
			
			$form_array =  array(
				'tag' => 'form',
				'id' => 'wp_super_edit_controller',
				'enctype' => 'application/x-www-form-urlencoded',
				'action' => htmlentities( $this->ui_form_url ),
				'method' => 'post',
				'content' => $form_contents,
				'return' => $return
			);
			
			if ( $onsubmit != '' ) $form_array['onsubmit'] = $onsubmit;
			
			if ( $return == true ) return $this->html_tag( $form_array );
			
			$this->html_tag( $form_array );
			
		}

		/**
		* Form table shell with WordPress admin classes
		* @param string $content
		* @param boolean $return
		* @return mixed
		*/
		function form_table( $content = '', $return = false ) {
			
			$content_array = array(
				'tag' => 'table',
				'class' => 'form-table',
				'content' => $content,
				'return' => $return
			);
			
			if ( $return == true ) return $this->html_tag( $content_array );
			
			$this->html_tag( $content_array );			
		}

		/**
		* Form table row for WordPress admin
		* @param string $header
		* @param string $content
		* @param boolean $return
		* @return mixed
		*/
		function form_table_row( $header = '', $content = '', $return = false ) {
			
			$row_content = $this->html_tag( array(
				'tag' => 'th',
				'scope' => 'row',
				'content' => $header,
				'return' => true
			) );
			
			$row_content .= $this->html_tag( array(
				'tag' => 'td',
				'content' => $content,
				'return' => true
			) );
			
			$content_array = array(
				'tag' => 'tr',
				'valign' => 'top',
				'content' => $row_content,
				'return' => $return
			);
			
			if ( $return == true ) return $this->html_tag( $content_array );
			
			$this->html_tag( $content_array );
		}

		/**
		* Form select produces select and options form element
		* @param string $option_name
		* @param array $options
		* @param string $selected
		* @param boolean $return
		* @return mixed
		*/
		function form_select( $option_name = '', $options = array(), $selected = '', $return = false ) {
			
			foreach( $options as $option_value => $option_text ) {
				$option_array = array(
					'tag' => 'option',
					'value' => $option_value,
					'content' => $option_text,
					'return' => true
				);			
				
				if ( $option_value == $selected ) $option_array['selected'] = 'selected';
				
				$option_content .= $this->html_tag( $option_array );
			}
			
			$content_array = array(
				'tag' => 'select',
				'name' => $option_name,
				'id' => $option_name,
				'content' => $option_content,
				'return' => $return
			);
			
			if ( $return == true ) return $this->html_tag( $content_array );
			
			$this->html_tag( $content_array );
		}
		
		/**
		* Display submit button
		* @param string $button_text button value
		* @param string $message description text
		* @param boolean $return
		* @return mixed
		*/
		function submit_button( $button_text = 'Update Options &raquo;', $message = '', $return = false ) {
			$content_array = array(
				'tag' => 'input',
				'tag_type' => 'single',
				'type' => 'submit',
				'name' => 'wp_super_edit_submit',
				'id' => 'wp_super_edit_submit_id',
				'class' => 'button',
				'value' => $button_text,
				'content' => $message,
				'return' => $return,
			);

			if ( $return == true ) return $this->html_tag( $content_array );
			
			$this->html_tag( $content_array );
		}

		/**
		* Display WP Super Edit administration menu
		*/
		function admin_menu_ui() {		
		
			$ui_tabs['buttons'] = $this->html_tag( array(
				'tag' => 'a',
				'href' => htmlentities( $this->ui_url . '&wp_super_edit_ui=buttons' ),
				'content' => __('Arrange Editor Buttons'),
				'return' => true
			) );
			$ui_tabs['plugins'] = $this->html_tag( array(
				'tag' => 'a',
				'href' => htmlentities( $this->ui_url . '&wp_super_edit_ui=plugins' ),
				'content' => __('Configure Editor Plugins'),
				'return' => true
			) );
			$ui_tabs['options'] = $this->html_tag( array(
				'tag' => 'a',
				'href' => htmlentities( $this->ui_url . '&wp_super_edit_ui=options' ),
				'content' => __('WP Super Edit Options'),
				'return' => true
			) );
			
			foreach ( $ui_tabs as $ui_tab => $ui_tab_html ) {

				if ( $ui_tab == $this->ui ) {
					$current_tab_html = $this->html_tag( array(
						'tag' => 'h3',
						'content' => $ui_tab_html,
						'return' => true
					) );
					$ui_tab_html = $current_tab_html;
				}
				
				$list = array(
					'tag' => 'li',
					'content' => $ui_tab_html,
					'return' => true
				);
				
				if ( $ui_tab == $this->ui ) $list['class'] = 'wp_super_edit_ui_current';
				
				$ui_tab_list .= $this->html_tag( $list );
			}
			
			$this->html_tag( array(
				'tag' => 'ul',
				'content' => $ui_tab_list,
				'id' => 'wp_super_edit_ui_menu'
			) );
			
		}

		/**
		* Display the current management mode
		*/
		function display_management_mode() {
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_management_mode',
				'content' => __('Management Mode: ') . $this->management_modes[ $this->management_mode ]
			) );
		}
		
		/**
		* Display installation user interfaces
		*/
		function install_ui() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_installer'
			) );
			
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_install_scanner',
				'class' => 'wp_super_edit_install',
				'content' => __('Click here to start the WP Super Edit installation by scanning your editor settings.')
			) );
			
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_install_wait',
				'class' => 'wp_super_edit_install',
				'content' => __('Please wait while we check your editor settings!')
			) );
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_install_form',
				'class' => 'wp_super_edit_install'
			) );
			
			$button = $this->submit_button( __('Install WP Super Edit'), __('<strong>Install default settings and database tables for WP Super Edit.</strong>'), true );
			
			$this->form( 'install', $button );
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );
		}

		/**
		* Display deactivation user interface
		*/
		function uninstall_ui() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_deactivate'
			) );
						
			$button = $this->submit_button( __('Uninstall WP Super Edit'), __('<strong>This option will remove settings and deactivate WP Super Edit. </strong>'), true );

			$this->form( 'uninstall', $button );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );
		}
		

		/**
		* Display WP Super Edit Options Interface
		*/
		function options_ui() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_settings'
			) );

			$this->display_management_mode();
			
			$submit_button = $this->submit_button( __('Update Options'), '', true );
			$submit_button_group = $this->html_tag( array(
				'tag' => 'p',
				'class' => 'submit',
				'content' => $submit_button,
				'return' => true
			) );
			
			$mode_select = $this->form_select( 'wp_super_edit_management_mode', $this->management_modes, $this->management_mode, true );
			
			$table_row = $this->form_table_row( __('Manage editor buttons using:'), $mode_select, true );

			$reset_default_user_box = $this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single-after',
				'type' => 'checkbox',
				'name' => 'wp_super_edit_reset_default_user',
				'id' => 'wp_super_edit_reset_default_user_i',
				'value' => 'reset_default_user',
				'content' => __('<br /> Reset Default User Setting to original scanned TinyMCE editor settings'),
				'return' => true
			) );

			$table_row .= $this->form_table_row( __('Reset Default User Settings:'), $reset_default_user_box, true );
			
			$reset_users_box = $this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single-after',
				'type' => 'checkbox',
				'name' => 'wp_super_edit_reset_users',
				'id' => 'wp_super_edit_reset_users_i',
				'value' => 'reset_users',
				'content' => __('<br /> Reset all users and roles using Default Editor Settings'),
				'return' => true
			) );
			
			$table_row .= $this->form_table_row( __('Reset All User and Role Settings:'), $reset_users_box, true );
			
			$rescan_plugins_box = $this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single-after',
				'type' => 'checkbox',
				'name' => 'wp_super_edit_rescan_plugins',
				'id' => 'wp_super_edit_rescan_plugins_i',
				'value' => 'rescan_plugins',
				'content' => __('<br /> Rescan plugins added to the WP Super Edit tinymce_plugins folder to add unregistered plugins and buttons.'),
				'return' => true
			) );
			
			$table_row .= $this->form_table_row( __('Rescan tinymce_plugins Folder:'), $rescan_plugins_box, true );			
			
			$form_content .= $this->form_table( $table_row, true );
			$form_content .= $submit_button_group;
			
			$this->form( 'options', $form_content );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );
			
			$this->uninstall_ui();

		}
		
		/**
		* Display WP Super Edit Plugins Interface
		*/
		function plugins_ui() {
		
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_settings'
			) );
			
			$submit_button = $this->submit_button( 'Update Options', '', true );
			$submit_button_group = $this->html_tag( array(
				'tag' => 'p',
				'class' => 'submit',
				'content' => $submit_button,
				'return' => true
			) );
			
			
			foreach ( $this->plugins as $plugin ) {
				
				$plugin_check_box_options = array(
					'tag' => 'input',
					'tag_type' => 'single-after',
					'type' => 'checkbox',
					'name' => "wp_super_edit_plugins[$plugin->name]",
					'id' => "wp_super_edit_plugins-$plugin->name",
					'value' => 'yes',
					'content' => '<br />' . $plugin->description,
					'return' => true
				);
				
				if ( $plugin->status == 'yes' ) $plugin_check_box_options['checked'] = 'checked';
				
				$plugin_check_box = $this->html_tag( $plugin_check_box_options );

				$table_row .= $this->form_table_row( $plugin->nicename , $plugin_check_box, true );
			}


			$form_content .= $this->form_table( $table_row, true );
			$form_content .= $submit_button_group;
			
			$this->form( 'plugins', $form_content );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );

		}
		
		/**
		* Output Javascript array for buttons.
		*
		* Javascript arrays are used for various client side actions including button positioning and dialog boxes.
		*/
		function buttons_js_objects() {
			foreach ( $this->buttons as $button ) {
				printf("\t\ttiny_mce_buttons['%s'] = new wp_super_edit_button( '%s', '%s' );\n", $button->name, $button->nicename, $button->description );
			}
		}


		/**
		* Display user management interfaces based on options.
		*/
		function user_management_ui() {
			global $wp_roles;
        	
        	switch ( $this->management_mode ) {
				case 'single':
					$user_management_text = __('This arrangement of visual editor buttons will apply to all users');
					break;
				case 'roles':
					$user_management_text = __('The arrangement of visual editor buttons will apply to all users in the selected Role or Default user button setting.<br />');
					
					$roles = Array();

					$roles['wp_super_edit_default'] = __('Default Button Settings');

					foreach( $wp_roles->role_names as $role => $name ) {
						$name = translate_with_context($name);
						$roles[$role] = $name;
						if ( $_REQUEST['wp_super_edit_manage_role'] == $role || $_REQUEST['wp_super_edit_user'] == $role ) {
							$selected = $role;
						}
					}					
					
					$role_select = $this->form_select( 'wp_super_edit_manage_role', $roles, $selected, true );
										
					$submit_button = $this->submit_button( __('Load Button Settings'), '', true );
					$submit_button_group = $this->html_tag( array(
						'tag' => 'p',
						'content' => __('Select User Role to Edit: ') . $role_select . $submit_button,
						'return' => true
					) );						
					
					$user_management_text .= $this->form( 'role_select', $submit_button_group, true, 'submitButtonConfig();' );		

					break;
				case 'users':
					$user_management_text = __('Users can arrange buttons under the Users tab. Changes to this button arrangement will only affect the defult button settings.');        	
					break;
				default:
					break;
				
        	}
        	
			$user_management_text = '<strong>' . $this->management_modes[ $this->management_mode ] . ':</strong> ' . $user_management_text;
			
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_user_management',
				'content' => $user_management_text
			) );
			
		}
		
		/**
		* Display WP Super Edit dragable button
		*/
		function make_button_ui( $button, $separator = false ) {
		
			$button_class = 'button_control';
			
			if ( $separator ) $button_class .= ' button_separator';
			
			$button_info_text = __('Button info for ');
			
			$button_info = $this->html_tag( array(
				'tag' => 'img',
				'tag_type' => 'single',
				'src' => $this->core_uri . 'images/info.png',
				'width' => '14',
				'height' => '16',
				'alt' => $button_info_text. $button->nicename,
				'title' => $button_info_text . $button->nicename,
				'onclick' => "getButtonInfo('$button->name');",
				'return' => true
			) );
			
			$separator_info_text = __('Toggle separator for ');
			
			$button_separator_toggle = $this->html_tag( array(
				'tag' => 'img',
				'tag_type' => 'single',
				'src' => $this->core_uri . 'images/separator.png',
				'width' => '14',
				'height' => '7',
				'alt' => $separator_info_text . $button->nicename,
				'title' => $separator_info_text . $button->nicename,
				'onclick' => "toggleSeparator('$button->name');",
				'return' => true
			) );
			
			$button_options = $this->html_tag( array(
				'tag' => 'div',
				'class' => 'button_info',
				'content' => $button_info . $button_separator_toggle,
				'return' => true
			) );
			
			$this->html_tag( array(
				'tag' => 'li',
				'id' => $button->name,
				'class' => $button_class,
				'content' => $button_options . $button->nicename,
			) );
		}

		
		/**
		* Display WP Super Edit Buttons Interface
		*/
		function buttons_ui() {
        	global $userdata;
        	
        	$user = 'wp_super_edit_default';
        	
        	switch ( $this->management_mode ) {
				case 'single':
					$user = 'wp_super_edit_default';
					break;
				case 'roles':
					if ( isset( $_REQUEST['wp_super_edit_manage_role'] ) )
						$user = $_REQUEST['wp_super_edit_manage_role'];
 
					if ( isset( $_REQUEST['wp_super_edit_user'] ) ) 
						$user = $_REQUEST['wp_super_edit_user'];
					
					break;
				case 'users':
					if ( $this->user_profile == true ) $user = $userdata->user_login; 
					break;	
				default:
					break;
			}
			
			if ( !$this->check_registered( 'user', $user ) ) {			
				$this->register_new_user( $user );
			}
			
			$current_user = $this->get_user_settings_ui( $user );
						
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_settings'
			) );			
			
			if ( !$this->user_profile ) $this->user_management_ui();
				
			$hidden_form_user = $this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single',
				'type' => 'hidden',
				'id' => 'i_wp_super_edit_user',
				'name' => 'wp_super_edit_user',
				'value' => $user,
				'return' => true
			) );
			
			$hidden_form_items .= $this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single',
				'type' => 'hidden',
				'id' => 'i_wp_super_edit_separators',
				'name' => 'wp_super_edit_separators',
				'value' => '',
				'return' => true
			) );
			
			for ( $button_row = 1; $button_row <= 4; $button_row += 1) {
			
				$hidden_form_items .= $this->html_tag( array(
					'tag' => 'input',
					'tag_type' => 'single',
					'type' => 'hidden',
					'id' => 'i_wp_super_edit_row_' . $button_row,
					'name' => 'wp_super_edit_row_' . $button_row,
					'value' => '',
					'return' => true
				) );
				
			}
			
			$action_options = array(
				'update' => __('Update Buttons'),
				'reset_default' => __('Reset to Defaults'),
				'set_default' => __('Set as Default')
			);

			if ( $user == 'wp_super_edit_default' ) {
				unset( $action_options['set_default'] );
				unset( $action_options['reset_default'] );
			}
			if ( $this->user_profile ) unset( $action_options['set_default'] );

			
			$set_default_controls = $this->form_select( 'wp_super_edit_action_control', $action_options, 'update', true );			

			$submit_button = $this->submit_button( __('Update Button Settings For: ') . $current_user['user_nicename'], $hidden_form_user . $hidden_form_items , true );								

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_button_save'
			) );

			$this->form( 'buttons', $submit_button . $set_default_controls, false, 'submitButtonConfig();' );
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'button_controls'
			) );
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'button_rows'
			) );

			
			for ( $button_row = 1; $button_row <= 4; $button_row += 1) {
				
				$this->html_tag( array(
					'tag' => 'h3',
					'class' => 'row_title',
					'content' => __('Editor Button Row ') . $button_row
				) );

				
				$this->html_tag( array(
					'tag' => 'ul',
					'tag_type' => 'open',
					'id' => 'row_section_' . $button_row,
					'class' => 'row_section'
				) );				
				
				foreach( $current_user['buttons'][$button_row] as $button_num => $button ) {

					$separator = false;
					
					if ( $current_user['buttons'][$button_row][$button_num +1] == '|' ) $separator = true;
					
					if ( $button == '|' ) continue;

					if ( !$this->check_registered( 'button', $button ) ) {
						$button_not_registered[] = $button;
						continue;
					}
										
					if ( !is_object( $this->active_buttons[$button] ) ) continue;
					
					$this->make_button_ui( $this->active_buttons[$button], $separator );
					
					$button_used[] = $button;
				
				}
				
				$this->html_tag( array(
					'tag' => 'ul',
					'tag_type' => 'close'
				) );

			}
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'disabled_buttons'
			) );
			
			$this->html_tag( array(
				'tag' => 'h3',
				'class' => 'row_title',
				'content' => __('Disabled Buttons')
			) );
		
			$this->html_tag( array(
				'tag' => 'ul',
				'tag_type' => 'open',
				'id' => 'row_section_disabled',
				'class' => 'row_section'
			) );
			
			foreach ( $this->active_buttons as $button => $button_options ) {
				if ( in_array( $button, $button_used ) ) continue;
				
				$this->make_button_ui( $this->active_buttons[$button] );

			}

			$this->html_tag( array(
				'tag' => 'ul',
				'tag_type' => 'close'
			) );							

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );
			
			$this->html_tag( array(
				'tag' => 'br',
				'class' => 'clear',
				'tag_type' => 'single'
			) );			
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );			

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );
			
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_dialog',
				'class' => 'hidden'
			) );

		}
 
    }

}

?>