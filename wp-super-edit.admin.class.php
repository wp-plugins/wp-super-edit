<?php

if ( class_exists( 'wp_super_edit_core' ) ) {
    
    class wp_super_edit_registry extends wp_super_edit_core {
        
        function check_registered( $type, $name ) {
        	global $wpdb;
 
			$name_col = 'name';
			
			if ( $type == 'plugin' ) {
				$db_table = $this->db_plugins;
			} elseif ( $type == 'button' ) {
				$db_table = $this->db_buttons;
			} elseif ( $type == 'user' ) {
				$db_table = $this->db_users;
				$name_col = 'user_name';
			}
			
			$register_check = $wpdb->get_row("
				SELECT name FROM $db_table
				WHERE $name_col='$name'
			");
			
			if ( $register_check->name == $name ) return true;
			
			return false;
			
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
			
			$plugin_values = '"' .
				$plugin['name'] . '", "' . 
				$plugin['nicename'] . '", "' . 
				$plugin['description'] . '", "' . 
				$plugin['provider'] . '", "' . 
				$plugin['status'] . '", "' . 
				$plugin['callbacks'] . '"'
			;
	
			$wpdb->query("
				INSERT INTO $this->db_plugins 
				(name, nicename, description, provider, status, callbacks) 
				VALUES ($plugin_values)
			");
        	
        }

        function register_tinymce_button( $button = array() ) {
        	global $wpdb;
			
			if ( $this->check_registered( 'button', $button['name'] ) ) return true;
			
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

        function register_user_settings( $user_name = 'wp_super_edit_default', $user_settings, $type = 'single',  $user_id = 'wp_super_edit' ) {
        	global $wpdb;
			
			$settings = maybe_serialize( $user_settings );
			$settings = $wpdb->escape( $settings );
			
			$user_values = '"' .
				$user_id . '", "' . 
				$user_name . '", "' . 
				$type . '", "' . 
				$settings . '"'
			;
			
			$wpdb->query("
				INSERT INTO $this->db_users 
				(user_id, user_name, user_type, editor_options) 
				VALUES ($user_values)
			");
					
		}

    }
    
    class wp_super_edit_admin extends wp_super_edit_core {

		public $ui;
		public $ui_url;
		public $ui_form_url;
		public $nonce;

		function init_ui() {
			$this->ui = ( !$_REQUEST['wp_super_edit_ui'] ? 'options' : $_REQUEST['wp_super_edit_ui'] );			
			if ( !$this->is_installed ) $this->ui = 'options';
			$this->ui_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'];
			$this->ui_form_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'] . '&wp_super_edit_ui=' . $this->ui;
			$this->nonce = 'wp-super-edit-update-key';
			
			if ( $this->ui == 'plugins' ) {
				 $this->get_plugins();
			}
		}

        function get_plugins() {
        	global $wpdb;
        	
			$this->plugins = $wpdb->get_results("
				SELECT name, nicename, description, provider, status FROM $this->db_plugins
			");
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
			
			$query = '';
			foreach ( $this->plugins as $plugin ) {
				if ( $_REQUEST['wp_super_edit_plugins'][$plugin->name] == 'yes' ) {
					$result = $wpdb->query( "
						UPDATE $this->db_plugins
						SET status='yes'
						WHERE name='$plugin->name'
						" );
				} else {
					$result = $wpdb->query( "
						UPDATE $this->db_plugins
						SET status='no'
						WHERE name='$plugin->name'
						" );
				}
			}
						
			$wpdb->print_error();
			
			$this->get_plugins();

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
		function form( $action = '', $content = '', $return = false ) {
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
		function form_select( $option_name = '', $options = array(), $return = false ) {
			
			foreach( $options as $option_value => $option_text ) {
				$option_array = array(
					'tag' => 'option',
					'value' => $option_value,
					'content' => $option_text,
					'return' => true
				);			
				
				if ( $option_value == $this->management_mode ) $option_array['selected'] = 'selected';
				
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
			
			$mode_select = $this->form_select( 'wp_super_edit_management_mode', $this->management_modes, true );
			
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
		* WP Super Edit Options Interface
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
 
    }

}

?>