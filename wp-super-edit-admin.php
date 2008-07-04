<?php
/**
* WP Super Edit Administration interface 
*
* These functions control the display for the administrative interface. This
* interface allows drag and drop control for buttons and interactive control for
* activating TinyMCE plugins. This interface requires a modern browser and 
* javascript.
*
* @package superedit_admin
*
*/


/**
* Set up administration interface
*
* Function used by Wordpress to initialize the adminsitrative interface.
*
*/
function wp_super_edit_admin_setup() {
	global $wp_super_edit, $wp_super_edit_admin;
	
	$wp_super_edit_admin = new wp_super_edit_admin;
	
	$wp_super_edit_admin->init_ui();
		
	$wp_super_edit_option_page = add_options_page( __('WP Super Edit', 'wp_super_edit'), __('WP Super Edit', 'wp_super_edit'), 5, 'wp-super-edit-admin.php', 'wp_super_edit_admin_page');

    if ( $wp_super_edit_admin->management_mode == 'users' ) {
		$wp_super_edit_user_page = add_submenu_page('profile.php', __('WP Super Edit', 'wp_super_edit'), __('WP Super Edit', 'wp_super_edit'), 0, 'wp-super-edit-admin.php', 'wp_super_edit_admin_page');
	}
	
	if ( strstr( $_GET['page'], 'wp-super-edit-admin' ) != false ) {

		if (  $_REQUEST['wp_super_edit_action'] == 'install' ) {
			check_admin_referer( 'wp_super_edit_nonce-' . $wp_super_edit_admin->nonce );
			include_once( $wp_super_edit->core_path . 'wp-super-edit-defaults.php');
			wp_super_edit_install_db_tables();
			wp_super_edit_wordpress_button_defaults();
			wp_super_edit_plugin_folder_scan();
			wp_super_edit_set_user_default();
		}
		
		if (  $_REQUEST['wp_super_edit_action'] == 'uninstall' ) {
			check_admin_referer( 'wp_super_edit_nonce-' . $wp_super_edit_admin->nonce );
			$wp_super_edit_admin->uninstall();
			$wp_super_edit_admin->is_installed = false;
		}
		
		if (  $_REQUEST['wp_super_edit_action'] == 'options' ) {
			check_admin_referer( 'wp_super_edit_nonce-' . $wp_super_edit_admin->nonce );
			$wp_super_edit_admin->do_options();
		}
		
		if (  $_REQUEST['wp_super_edit_action'] == 'plugins' ) {
			check_admin_referer( 'wp_super_edit_nonce-' . $wp_super_edit_admin->nonce );
			$wp_super_edit_admin->do_plugins();
		}
	
		if ( $wp_super_edit_admin->ui == 'buttons' ) {

			wp_enqueue_script( 'wp-super-edit-dimensions',  '/wp-content/plugins/wp-super-edit/js/jquery.dimensions.pack.js', array('jquery'), '2135' );
			wp_enqueue_script( 'wp-super-edit-ui',  '/wp-content/plugins/wp-super-edit/js/jquery-ui-all-1.5rc1.packed.js', false, '2135' );
			wp_enqueue_script( 'wp-super-edit-greybox',  '/wp-content/plugins/wp-super-edit/js/greybox.js', false, '2135' );
			wp_enqueue_script( 'wp-super-edit-history',  '/wp-content/plugins/wp-super-edit/js/jquery.history_remote.pack.js', false, '2135' );
			
			add_action('admin_footer', 'wp_super_edit_admin_footer');
		}

		add_action('admin_head', 'wp_super_edit_admin_head');

	}
}

/**
* Add javascript and css to the HEAD area
*
* Some complex CSS and javascript functions to operate the WP Super Edit advanced interface.
*
* @global array $superedit_ini 
*/
function wp_super_edit_admin_head() {
	global $wp_super_edit_admin;
?>

	<link rel="stylesheet" href="<?php echo $wp_super_edit_admin->core_uri ?>css/wp_super_edit.css" type="text/css" />
		
	<?php if ( $wp_super_edit_admin->is_installed == true ) return; ?>
	
	<script type='text/javascript'>
	/* <![CDATA[ */
		jQuery(document).ready( function() {
						
			jQuery( '#wp_super_edit_install_form' ).hide();
			jQuery( '#wp_super_edit_install_wait' ).hide();

			jQuery( '#wp_super_edit_install_scanner' ).click( function() {
			
				jQuery( '#wp_super_edit_install_scanner' ).fadeOut();
				jQuery( '#wp_super_edit_install_wait' ).fadeIn();

				jQuery( '#wp_super_edit_null' ).load( 
					'<?php bloginfo( 'wpurl' ); ?>/wp-includes/js/tinymce/tiny_mce_config.php', 
					{ scan: 'wp_super_edit_tinymce_scan', uncache: <?php echo rand( 100, 500 ); ?> },
					function() {
						jQuery( '#wp_super_edit_install_wait' ).fadeOut();
						jQuery( '#wp_super_edit_install_form' ).fadeIn();
					}
				);
			} );
		} );
	/* ]]> */
	</script>


<?php
}


/**
* Display messages on options update.
*
* Uses array_walk to build table, rows, and cells for either buttons or plugins.
* Includes javascript functions for drag drop button interface
*
* @param $message Optional notification message.
* @global $wp_version
*/
function superedit_update_message( $message = '' ) {
	global $wp_version;
?>
	<div class="fade updated" id="message">
		<?php $writepost = ($wp_version >= 2.1 ) ? '/wp-admin/post-new.php' : '/wp-admin/post.php'; ?> 
		<p>
			<?php printf(__('WP Super Edit Settings Updated. Remember... Reload your editor or empty your cache and <a href="%s">Go Write Something!!</a> &raquo;'), get_bloginfo('wpurl') . $writepost . '?up='. rand(101, 199) ); ?>
		</p>
		<p style="color:red;">
			In most cases you will need to RELOAD the editor page, in some extreme cases you may need to EMPTY YOUR BROWSER CACHE before your new options will be available.
		</p>
		<?php if ( $message != '' ) : ?>
		<p>
			<?php echo $message ?>
		</p>		
		<?php endif; ?>
		
	</div>       
<?php
}



/**
* Display Advanced WP Super Edit interface
*
* Very advanced control interface for TinyMCE buttons and plugins using
* drag and drop.
*
* @global array $superedit_ini 
* @global array $superedit_options
* @global array $superedit_buttons
* @global array $superedit_plugins
*/
function wp_super_edit_admin_page() {
	global $wp_super_edit, $wp_super_edit_admin;
		
	$updated = false;
	
	$wp_super_edit_admin->ui_header();
	
	if ( !$wp_super_edit_admin->is_installed && $_REQUEST['wp_super_edit_action'] != 'install' ) {
		$wp_super_edit_admin->install_ui();
		$wp_super_edit_admin->ui_footer();
		return;
	}

	if (  $_REQUEST['wp_super_edit_action'] == 'uninstall' ) {
		$wp_super_edit_admin->install_ui();
		$wp_super_edit_admin->ui_footer();
		return;
	}	
	
	// Plugin options form
	?>

		<?php $wp_super_edit_admin->admin_menu_ui(); ?>

		<?php if ( !$wp_super_edit_admin->ui || $wp_super_edit_admin->ui == 'buttons' ) : ?>
			<?php $wp_super_edit_admin->buttons_ui(); ?>
		<?php endif; ?>
		
		<?php if ( $wp_super_edit_admin->ui == 'plugins' ) : ?>	
			<?php $wp_super_edit_admin->plugins_ui(); ?>
		<?php endif; ?>
		
		<?php if ( $wp_super_edit_admin->ui == 'options' ) : ?>	
			<?php $wp_super_edit_admin->options_ui(); ?>
		<?php endif; ?>
		
		<?php $wp_super_edit_admin->ui_footer(); ?>

<?php 
}


/**
* Add javascript to the FOOTER area
*
* Some complex CSS and javascript functions to operate the WP Super Edit advanced interface.
*
* @global array $superedit_ini 
*/
function wp_super_edit_admin_footer() {
	global $wp_super_edit_admin;

?>

<script type="text/javascript">
	// <![CDATA[

	// Define custom jQuery namespace to keep away javascript conflicts
	var wpsuperedit = jQuery.noConflict();

	// Default Variables and Objects
		
	function wp_super_edit_button( desc, notice, status, row, position, separator, plugin ) {
		this.desc = desc;
		this.notice = notice;
		this.status = status;
		this.row = row;    		
		this.position = position;
		this.separator = separator;    
		this.plugin = plugin;
	  }
	
	<?php $wp_super_edit_admin->buttons_js_objects(); ?>
	
	var data;
	var tiny_mce_buttons = new Object();
	var buttons = new Array();
	
	
	// Plugin and Button Control Functions
	
	function toggleSeparator(button) {
		wpsuperedit( '#' + button ).toggleClass( 'button_separator' );
		
		if ( wpsuperedit('#sval_' + button ).attr('value') == 'Y' ) {
			wpsuperedit( '#sval_' + button ).attr('value','N');
		} else {
			wpsuperedit( '#sval_' + button ).attr('value','Y');
		}
		
	}

	function getButtonInfo(button) {
	
		wpsuperedit.GB_show('about:blank', {
				close_img: "<?php echo $wp_super_edit->core_uri ?>images/close.gif",
				height: 280,
				width: 300,
				animation: true,
				overlay_clickable: true,
				caption: 'Editor Button Information'
		});
		
		// We don't want the iframe greybox gives us: 
		wpsuperedit('#GB_frame').remove();
		wpsuperedit("#GB_window").append("<div id='GB_frame'></div>");
		wpsuperedit("#GB_frame").append("<h3>" + tiny_mce_buttons[button].desc + "</h3><p>" + tiny_mce_buttons[button].notice + "</p>");

		return false;		
	}	

	wpsuperedit(document).ready(
		function() {

			// Controls Drag + Drop
			wpsuperedit('#row1').sortable(
				{
					connectWith: ['#tinymce_buttons', '#row2', '#row3' ],
					scroll: true,
					placeholder: 'sort_placeholder',
					opacity: 0.7
				}
			);
	
			wpsuperedit('#row2').sortable(
				{
					connectWith: ['#tinymce_buttons', '#row1', '#row3' ],
					scroll: true,
					placeholder: 'sort_placeholder',
					opacity: 0.7
				}
			);		
			
			wpsuperedit('#row3').sortable(
				{
					connectWith: ['#tinymce_buttons', '#row1', '#row2' ],
					scroll: true,
					placeholder: 'sort_placeholder',
					opacity: 0.7
				}
			);		
			
			wpsuperedit('#tinymce_buttons').sortable(
				{
					connectWith: ['#row1', '#row2', '#row3' ],
					scroll: true,
					placeholder: 'sort_placeholder',
					opacity: 0.7
				}
			);

			// Set up values for form submission
			wpsuperedit('#tinymce_controller').submit(
				
				function() {
				
					serial2 = wpsuperedit.SortSerialize();
								
					wpsuperedit.each( serial2.o.tinymce_buttons, function(i, n){
					  wpsuperedit( '#bval_' + n ).attr('value','N');			  
					});			
		
					wpsuperedit.each( serial2.o.row1, function(i, n){
					  wpsuperedit('#bval_' + n ).attr('value','Y');
					  wpsuperedit( '#o_row_1' ).attr('value', serial2.o.row1 );
					  
					});
					
					wpsuperedit.each( serial2.o.row2, function(i, n){
					  wpsuperedit('#bval_' + n ).attr('value','Y');
					  wpsuperedit( '#o_row_2' ).attr('value', serial2.o.row2 );
		
					});
					
					wpsuperedit.each( serial2.o.row3, function(i, n){
					  wpsuperedit('#bval_' + n ).attr('value','Y');
					  wpsuperedit( '#o_row_3' ).attr('value', serial2.o.row3 );
		
					});
						
				}
			);
						
		}
	);

	// ]]>
</script> 
<?php
}

// End - Superedit Admin Panel //
?>
