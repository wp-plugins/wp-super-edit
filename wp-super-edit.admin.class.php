<?php

if ( class_exists( 'wp_super_edit_core' ) ) {
    
    class wp_super_edit_admin extends wp_super_edit_core {

		var $ui;
		var $ui_url;
		var $ui_form_url;
		var $nonce;
		var $plugins;
		var $buttons;
		var $active_buttons;
		
		var $user_profile;

		function init_ui() {
			$this->ui = ( !$_REQUEST['wp_super_edit_ui'] ? 'options' : $_REQUEST['wp_super_edit_ui'] );			
			if ( !$this->is_installed ) $this->ui = 'options';
			
			if ( strstr( $_SERVER['PHP_SELF'], 'users.php' ) != false ) {
				$this->user_profile = true;
				$this->ui = 'buttons';
			}
			
			$this->ui_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'];
			$this->ui_form_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'] . '&wp_super_edit_ui=' . $this->ui;
			$this->nonce = 'wp-super-edit-update-key';
			
			if ( $this->ui == 'plugins' ) {
				 $this->get_plugins();
			}
			
			if ( $this->ui == 'buttons' ) {
				 $this->get_buttons();
				 $this->get_active_buttons();
			}
		}

        function get_plugins() {
        	global $wpdb;
        	
			$this->plugins = $wpdb->get_results("
				SELECT name, nicename, description, provider, status 
				FROM $this->db_plugins
			");
        }       
        
        function get_buttons() {
        	global $wpdb;
        	
			$buttons = $wpdb->get_results("
				SELECT name, nicename, description, provider, status 
				FROM $this->db_buttons
			");
			
			foreach( $buttons as $button ) {
				$this->buttons[$button->name] = $button;
			}
        }
        
         function get_active_buttons() {
        	global $wpdb;
        	
			$buttons = $wpdb->get_results("
				SELECT name, nicename, description, provider, status 
				FROM $this->db_buttons
				WHERE status='yes'
			");
			
			foreach( $buttons as $button ) {
				$this->active_buttons[$button->name] = $button;
			}
        }       
        

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
		* Uninstall plugin
		*
		* Function used when to clear settings.
		*
		*/
		function uninstall() {
			global $wpdb;
			
			$wpdb->query('DROP TABLE IF EXISTS ' . $this->db_options );
			$wpdb->query('DROP TABLE IF EXISTS ' . $this->db_plugins );
			$wpdb->query('DROP TABLE IF EXISTS ' . $this->db_buttons );
			$wpdb->query('DROP TABLE IF EXISTS ' . $this->db_users );
			
			delete_option( 'wp_super_edit_tinymce_scan' );
			
			$this->is_installed = false;

			// $url = add_query_arg( '_wpnonce', wp_create_nonce( 'deactivate-plugin_wp-super-edit/wp-super-edit.php' ), get_bloginfo('wpurl') . '/wp-admin/plugins.php?action=deactivate&plugin=wp-super-edit/wp-super-edit.php' );
			// wp_redirect( $url );

		}

		/**
		* Options 
		*
		* Function used to set options from form.
		*
		*/
		function do_options() {
			global $wpdb;
			
			$this->set_option( 'management_mode', $wpdb->escape( $_REQUEST['wp_super_edit_management_mode'] ) );
			$this->management_mode = $this->get_option( 'management_mode' );

		}
		
		/**
		* Options 
		*
		* Function used to set options from form.
		*
		*/
		function do_plugins() {
			global $wpdb;
			
			foreach ( $this->plugins as $plugin ) {
				if ( $_REQUEST['wp_super_edit_plugins'][$plugin->name] == 'yes' ) {
					$status = 'yes';
				} else {
					$status = 'no';
				}
				
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
									
			$this->get_plugins();
		}
		
		/**
		* Options 
		*
		* Function used to set options from form.
		*
		*/
		function do_buttons() {
			global $wpdb;
						
			$current_settings = $this->get_user_settings_ui( $_REQUEST['wp_super_edit_user'] );
			$current_user_settings = $current_settings['editor_options'];
			unset( $current_settings );
			
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
			
			
			$this->update_user_settings( $_REQUEST['wp_super_edit_user'], $current_user_settings );

		}


        
        function get_registered() {
        	global $wpdb;
        	
			$this->registered_buttons = $wpdb->get_results("
				SELECT name FROM $this->db_buttons
			");
			$this->registered_plugins = $wpdb->get_results("
				SELECT name FROM $this->db_plugins
			");
        }

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

        function register_user_settings( $user_name = 'wp_super_edit_default', $user_nicename = 'Default Editor Settings', $user_settings, $type = 'single' ) {
        	global $wpdb;
			
			$settings = maybe_serialize( $user_settings );

			$wpdb->query( $wpdb->prepare( "
				INSERT INTO $this->db_users
				( user_name, user_nicename, user_type, editor_options ) 
				VALUES ( %s, %s, %s, %s )", 
				$user_name, $user_nicename, $type, $settings 
			) );
					
		}

        function update_user_settings(  $user_name = 'wp_super_edit_default', $user_settings ) {
        	global $wpdb;
			
			$settings = maybe_serialize( $user_settings );
			
			$management_mode = ( $user_name == 'wp_super_edit_default' ? 'single' : $this->management_mode );

			$wpdb->query( $wpdb->prepare( "
				UPDATE $this->db_users
				SET editor_options = %s 
				WHERE user_name = %s AND user_type = %s", 
				$settings, $user_name, $management_mode 
			) );
					
		}

		function register_new_user( $user_name ) {
        	global $wpdb, $wp_roles, $userdata;

        	switch ( $this->management_mode ) {
				case 'single':
					return;
				case 'roles':
					if ( isset( $wp_roles->role_names[$user_name] ) ) {
						$nice_name = translate_with_context( $wp_roles->role_names[$user_name] );
						$user_settings = $this->get_user_settings( 'wp_super_edit_default' );
						$editor_options = maybe_unserialize( $user_settings->editor_options );
						$this->register_user_settings( $user_name, $nice_name, $editor_options, $this->management_mode );
					}
					break;
				case 'users':
					$user_settings = $this->get_user_settings( 'wp_super_edit_default' );
					$editor_options = maybe_unserialize( $user_settings->editor_options );
					$this->register_user_settings( $userdata->user_login, 'user', $editor_options, $this->management_mode );
					break;	
				default:
					break;
			}
		
		}


		/**
		* Display html tag with attributes
		* @param array $html_options options and content to display
		*/
		
		/*
			$format = 'The %2$s contains %1$d monkeys.
					   That\'s a nice %2$s full of %1$d monkeys.';
			printf($format, $num, $location);
			
			echo "var is ".($var < 0 ? "negative" : "positive"); 
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
		*/		
		function nonce_field($action = -1) { 
			return wp_nonce_field( $action, "_wpnonce", true , false );
		}
		
		/**
		* WP Super Edit admin display header and information
		* @param string $text text to display
		*/
		function ui_header() {
		
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'class' => 'wrap',
			) );
			
			$this->html_tag( array(
				'tag' => 'h2',
				'content' => 'WP Super Edit',
			) );

			$this->html_tag( array(
				'tag' => 'p',
				'content' => 'To give you more control over the Wordpress TinyMCE WYSIWYG Visual Editor. For more information please vist the <a href="http://factory.funroe.net/projects/wp-super-edit/">WP Super Edit project.</a>',
			) );
						
		}

		/**
		* WP Super Edit admin display footer
		* @param string $text text to display
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
		* Start WP Super Edit admin form
		* @param string $text text to display
		*/
		function form( $action = '', $content = '', $return = false, $onsubmit = '' ) {
			global $wp_super_edit_nonce;
			
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
			
			if ( $onsubmit != '' ) $form_array['onSubmit'] = $onsubmit;
			
			if ( $return == true ) return $this->html_tag( $form_array );
			
			$this->html_tag( $form_array );
			
		}

		/**
		* Form Table
		* @param string $text text to display
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
		* Form Table Row
		* @param string $text text to display
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
		* Form Select
		* @param string $text text to display
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
		* Create administration menu
		* 
		*/
		function admin_menu_ui() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_ui_menu'
			) );		
		
		
			$ui_tabs['buttons'] = $this->html_tag( array(
				'tag' => 'a',
				'href' => htmlentities( $this->ui_url . '&wp_super_edit_ui=buttons' ),
				'content' => 'Arrange Editor Buttons',
				'return' => true
			) );
			$ui_tabs['plugins'] = $this->html_tag( array(
				'tag' => 'a',
				'href' => htmlentities( $this->ui_url . '&wp_super_edit_ui=plugins' ),
				'content' => 'Configure Editor Plugins',
				'return' => true
			) );
			$ui_tabs['options'] = $this->html_tag( array(
				'tag' => 'a',
				'href' => htmlentities( $this->ui_url . '&wp_super_edit_ui=options' ),
				'content' => 'Super Edit Options',
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
				'content' => $ui_tab_list
			) );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );
			
		}

		/**
		* Show the management mode
		* 
		*/
		function display_management_mode() {
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_management_mode',
				'content' => 'Management Mode: ' . $this->management_modes[ $this->management_mode ]
			) );
		}
		
		/**
		* Create deactivation user interface
		* 
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
				'content' => 'Click here to start the WP Super Edit installation by scanning your editor settings.'
			) );
			
			$this->html_tag( array(
				'tag' => 'div',
				'id' => 'wp_super_edit_install_wait',
				'class' => 'wp_super_edit_install',
				'content' => 'Please wait while we check your editor settings!'
			) );
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_install_form',
				'class' => 'wp_super_edit_install'
			) );
			
			$button = $this->submit_button( 'Install WP Super Edit', '<strong>Install default settings and database tables for WP Super Edit.</strong>', true );
			
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
		* Create deactivation user interface
		* 
		*/
		function uninstall_ui() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_deactivate'
			) );
						
			$button = $this->submit_button( 'Uninstall WP Super Edit', '<strong>This option will remove settings and deactivate WP Super Edit. </strong>', true );

			$this->form( 'uninstall', $button );

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
			) );
			
		}
		

		/**
		* WP Super Edit Options Interface
		* 
		*/
		function options_ui() {
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_options'
			) );

			$this->display_management_mode();
			
			$submit_button = $this->submit_button( 'Update Options', '', true );
			$submit_button_group = $this->html_tag( array(
				'tag' => 'p',
				'class' => 'submit',
				'content' => $submit_button,
				'return' => true
			) );
			
			$mode_select = $this->form_select( 'wp_super_edit_management_mode', $this->management_modes, $this->management_mode, true );
			
			$table_row = $this->form_table_row( 'Manage editor buttons using:', $mode_select, true );
			
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
		* WP Super Edit Plugins Interface
		* 
		*/
		function plugins_ui() {
		
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_plugins'
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
					'content' => "<br /> $plugin->description",
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
		* Creates Javascript array for buttons and plugins.
		*
		* Javascript arrays are used for various client side actions including button positioning and dialog boxes.
		*
		* @param $settings Button or plugin array.
		* @param $name Button or plugin name.
		* @param $type Define as button or plugin.
		* @global array $superedit_ini
		*/
		function buttons_js_objects() {
			foreach ( $this->buttons as $button ) {
				printf("\t\ttiny_mce_buttons['%s'] = new wp_super_edit_button( '%s', '%s' );\n", $button->name, $button->nicename, $button->description );
			}
		}


		/**
		* User management
		* 
		*/
		function user_management_ui() {
			global $wp_roles;
        	
        	switch ( $this->management_mode ) {
				case 'single':
					$user_management_text = 'This arrangement of visual editor buttons will apply to all users';
					break;
				case 'roles':
					$user_management_text = 'The arrangement of visual editor buttons will apply to all users in the selected Role or Default user button setting.<br />';
					
					$roles = Array();

					$roles['wp_super_edit_default'] = 'Default Button Settings';

					foreach( $wp_roles->role_names as $role => $name ) {
						$name = translate_with_context($name);
						$roles[$role] = $name;
						if ( $_REQUEST['wp_super_edit_manage_role'] == $role || $_REQUEST['wp_super_edit_user'] == $role ) {
							$selected = $role;
						}
					}					
					
					$role_select = $this->form_select( 'wp_super_edit_manage_role', $roles, $selected, true );
										
					$submit_button = $this->submit_button( 'Load Button Settings', '', true );
					$submit_button_group = $this->html_tag( array(
						'tag' => 'p',
						'content' => 'Select User Role to Edit: ' . $role_select . $submit_button,
						'return' => true
					) );						
					
					$user_management_text .= $this->form( 'role_select', $submit_button_group, true, 'submitButtonConfig();' );		

					break;
				case 'users':
					$user_management_text = 'Users can arrange buttons under the Users tab. Changes to this button arrangement will only affect the defult button settings.';        	
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
		* WP Super Edit Make Dragable Buttons
		* 
		*/
		function make_button_ui( $button, $separator = false ) {
		
			$button_class = 'button_control';
			
			if ( $separator ) $button_class .= ' button_separator';
			
			$button_info = $this->html_tag( array(
				'tag' => 'img',
				'tag_type' => 'single',
				'src' => $this->core_uri . 'images/info.png',
				'width' => '14',
				'height' => '16',
				'alt' => 'Button info for ' . $button->nicename,
				'title' => 'Button info for ' . $button->nicename,
				'onClick' => "getButtonInfo('$button->name');",
				'return' => true
			) );
			
			$button_separator_toggle = $this->html_tag( array(
				'tag' => 'img',
				'tag_type' => 'single',
				'src' => $this->core_uri . 'images/separator.png',
				'width' => '14',
				'height' => '7',
				'alt' => 'Toggle separator for' . $button->nicename,
				'title' => 'Toggle separator for ' . $button->nicename,
				'onClick' => "toggleSeparator('$button->name');",
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
		* WP Super Edit Buttons Interface
		* 
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
			
			if ( !$this->user_profile ) $this->user_management_ui();
				
			$hidden_form_items = $this->html_tag( array(
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
						
			$submit_button = $this->submit_button( 'Update Button Settings For: ' . $current_user['user_nicename'], $hidden_form_items , true );

			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_buttons'
			) );	

			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_button_save'
			) );

			$this->form( 'buttons', $submit_button, false, 'submitButtonConfig();' );
			
			$this->html_tag( array(
				'tag' => 'div',
				'tag_type' => 'close'
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
					'content' => "Editor Button Row $button_row"
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
				'content' => "Disabled Buttons"
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