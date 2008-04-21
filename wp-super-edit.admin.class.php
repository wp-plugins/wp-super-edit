<?php

if ( class_exists( 'wp_super_edit_core' ) ) {

    class wp_super_edit_db extends wp_super_edit_core { 
    
        
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

        function set_user_settings( $username = 'default' ) {
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
    
    class wp_super_edit_registry extends wp_super_edit_db {
        
        function check_registered( $type, $name ) {
        	global $wpdb;
 
			if ( $type == 'plugin' ) {
				$db_table = $this->db_plugins;
			} else {
				$db_table = $this->db_buttons;
			}
			
			$register_check = $wpdb->get_row("
				SELECT name FROM $db_table
				WHERE name='$name'
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
			
			if ( !$this->check_registered( 'plugin', $plugin['name'] ) ) return true;
			
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

    }
    
    class wp_super_edit_ui extends wp_super_edit_core {

		public $ui;
		public $ui_url;
		public $ui_form_url;

		function init_ui() {
			$this->ui = ( !$_REQUEST['wp_super_edit_ui'] ? 'buttons' : $_REQUEST['wp_super_edit_ui'] );			
			if ( !$this->is_db_installed ) $this->ui = 'options';
			$this->ui_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'];
			$this->ui_form_url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'] . '&wp_super_edit_ui=' . $this->ui;	
		}

		/**
		* Display text in enclosed <p> with classes
		* @param string $text text to display
		*/
		function admin_p( $text, $class = '' ) {
			if ( $class != '' ) $class_text = ' class="' . $class . '"';
?>
			<p<?php echo $class_text; ?>><?php echo $text; ?></p>
<?php 
		}
		
		/**
		* Start WP Super Edit admin form
		* @param string $text text to display
		*/
		function form_start() {
?>
			<form id="tinymce_controller" enctype="application/x-www-form-urlencoded" action="<?php echo $this->ui_form_url; ?>" method="post">
				<?php wp_super_edit_nonce_field('$wp_super_edit_nonce', $wp_super_edit_nonce); ?>
<?php
		}

		/**
		* End WP Super Edit admin form
		* @param string $text text to display
		*/
		function form_end() {
?>
			</form>
<?php
		}

		/**
		* Display text in enclosed <p> with classes
		* @param string $text text to display
		*/
		function html_input( $html_options = array() ) {

			$html_attributes = '';
			
			foreach ( $html_options as $name => $option ) {
				if ( $name == 'text' ) continue;
				if ( $name == 'return' ) continue;
				$html_attributes .= ' ' . $name . '="' . $option . '"';
			}
			
			if ( $html_options['return'] == true ) return $html_options['text'] . "<input$html_attributes />";
?>
			<?php echo $html_options['text']; ?> <input<?php echo $html_attributes; ?>/>
<?php 
		}

		/**
		* Display submit button
		* @param string $button_text button value
		* @param string $message description text
		*/
		function submit_button( $button_text = 'Update Options &raquo;', $message = '' ) {
			$button = $this->html_input ( array(
				'type' => 'submit',
				'name' => 'wp_super_edit_submit',
				'id' => 'wp_super_edit_submit_id',
				'value' => $button_text,
				'text' => $message,
				'return' => true,
			) );
			$this->admin_p( $button, 'submit clearer' );
		}

		/**
		* WP Super Edit hidden action
		* @param string $value set value for wp_super_edit_action hidden form input
		*/
		function wp_super_edit_action( $value = '' ) {
			$this->html_input ( array(
				'type' => 'hidden',
				'name' => 'wp_super_edit_action',
				'id' => 'wp_super_edit_action_id',
				'value' => $value
			) );
		}


		/**
		* Create deactivation user interface
		* @param string $text text to display
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
 
    }

}

?>