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
		}

		function nonce_field($action = -1) { 
			return wp_nonce_field($action);
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

			// $url = add_query_arg( '_wpnonce', wp_create_nonce( 'deactivate-plugin_wp-super-edit/wp-super-edit.php' ), get_bloginfo('wpurl') . '/wp-admin/plugins.php?action=deactivate&plugin=wp-super-edit/wp-super-edit.php' );
			// wp_redirect( $url );

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
		* Display html input tag with attributes
		* @param string $text text to display
		*/
		function html_select( $html_options = array() ) {

			$html_attributes = '';
			
			foreach ( $html_options as $name => $option ) {
				if ( $name == 'text' ) continue;
				if ( $name == 'return' ) continue;
				if ( $name == 'select_options' ) continue;
				if ( $name == 'selected' ) continue;
				
				$html_attributes .= ' ' . $name . '="' . $option . '"';
			}
			
			if ( $html_options['return'] == true ) return $html_options['text'] . "<select$html_attributes />";
?>
			<?php echo $html_options['text']; ?> <select<?php echo $html_attributes; ?>/>
<?php 
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
			
			$this->admin_menu_ui();
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
				'id' => 'wp-super-edit-null',
			) );
		}
		
		/**
		* Start WP Super Edit admin form
		* @param string $text text to display
		*/
		function form_start() {
			global $wp_super_edit_nonce;
			
			$this->html_tag( array(
				'tag' => 'form',
				'tag_type' => 'open',
				'id' => 'wp_super_edit_controller',
				'enctype' => 'application/x-www-form-urlencoded',
				'action' => $this->ui_form_url,
				'method' => 'post'
			) );
			$this->nonce_field('wp_super_edit_nonce-' . $this->nonce);
		}

		/**
		* End WP Super Edit admin form
		* @param string $text text to display
		*/
		function form_end() {
			$this->html_tag( array(
				'tag' => 'form',
				'tag_type' => 'close',
			) );
		}

		/**
		* Display submit button
		* @param string $button_text button value
		* @param string $message description text
		*/
		function submit_button( $button_text = 'Update Options &raquo;', $message = '' ) {
			$button = $this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single',
				'type' => 'submit',
				'name' => 'wp_super_edit_submit',
				'id' => 'wp_super_edit_submit_id',
				'class' => 'button',
				'value' => $button_text,
				'content' => $message,
				'return' => true,
			) );
			$this->html_tag( array(
				'tag' => 'p',
				'class' => 'submit clearer',
				'content' => $button
			) );
		}

		/**
		* WP Super Edit hidden action
		* @param string $value set value for wp_super_edit_action hidden form input
		*/
		function wp_super_edit_action( $value = '' ) {
			$this->html_tag( array(
				'tag' => 'input',
				'tag_type' => 'single',
				'type' => 'hidden',
				'name' => 'wp_super_edit_action',
				'id' => 'wp_super_edit_action_id',
				'class' => 'button',
				'value' => $value,
			) );
		}

		/**
		* Create administration menu
		* 
		*/
		function admin_menu_ui() {			
?>
			<div id="wp-super-edit-ui-menu">
				<ul>
					<li><a href="<?php echo $this->ui_url; ?>&wp_super_edit_ui=buttons">Arrange Editor Buttons</a></li>
					<li><a href="<?php echo $this->ui_url; ?>&wp_super_edit_ui=plugins">Configure Editor Plugins</a></li>
					<li><a href="<?php echo $this->ui_url; ?>&wp_super_edit_ui=options">Super Edit Options</a></li>
				</ul>
			</div>
<?php
		}

		/**
		* Create deactivation user interface
		* 
		*/
		function install_ui() {
?>
		<div id="wp_super_edit_install">
			<?php $this->form_start(); ?>
			<?php $this->wp_super_edit_action( 'install' ); ?>
			<?php $this->submit_button( 'Install WP Super Edit', '<strong>Install default settings and database tables for WP Super Edit.</strong>' ); ?>
			<?php $this->form_end(); ?>
		</div>
<?php
		}

		/**
		* Create deactivation user interface
		* 
		*/
		function uninstall_ui() {
?>
		<div id="wp_super_edit_deactivate">
			<?php $this->form_start(); ?>
			<?php $this->wp_super_edit_action( 'uninstall' ); ?>
			<?php $this->submit_button( 'Uninstall WP Super Edit', '<strong>This option will remove settings and deactivate WP Super Edit. </strong>' ); ?>
			<?php $this->form_end(); ?>
		</div>
<?php
		}
		

		/**
		* Create deactivation user interface
		* 
		*/
		function options_ui() {
?>
		<div id="wp_super_edit_options">
			<h3>WP Super Edit Options</h3>
			<?php $this->form_start(); ?>
			<?php $this->wp_super_edit_action( 'options' ); ?>
			
			<label for="wp_super_edit_management_mode">Manage editor buttons using: </label>
			<select name="wp_super_edit_management_mode" id="wp_super_edit_management_mode">
				<option value="single">One editor setting for all users</option>
				<option value="roles">Role based editor settings</option>
				<option value="users">Individual user editor settings</option>
			</select>
			
			
			<?php $this->submit_button( 'Update Options' ); ?>
			<?php $this->form_end(); ?>
			<?php $this->uninstall_ui(); ?>
		</div>
<?php
		}
 
    }

}

?>