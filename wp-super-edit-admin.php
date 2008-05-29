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
	
	$wp_super_edit_admin = new wp_super_edit_ui;
	
	$wp_super_edit_admin->init_ui();
		
	$page = add_options_page( __('WP Super Edit', 'wp_super_edit'), __('WP Super Edit', 'wp_super_edit'), 5, 'wp-super-edit-admin.php', 'wp_super_edit_admin_page');
		
	if ( strstr( $_GET['page'], 'wp-super-edit-admin' ) != false ) {
		
		if ( $wp_super_edit_admin->ui == 'buttons' ) {

			wp_enqueue_script( 'wp-super-edit-dimensions',  '/wp-content/plugins/wp-super-edit/js/jquery.dimensions.pack.js', array('jquery'), '2135' );
			wp_enqueue_script( 'wp-super-edit-ui',  '/wp-content/plugins/wp-super-edit/js/jquery.ui-all-1.5b2.packed.js', false, '2135' );

			wp_enqueue_script( 'wp-super-edit-greybox',  '/wp-content/plugins/wp-super-edit/js/greybox.js', false, '2135' );
			wp_enqueue_script( 'wp-super-edit-history',  '/wp-content/plugins/wp-super-edit/js/jquery.history_remote.pack.js', false, '2135' );
			

			
			add_action('admin_footer', 'superedit_admin_footer');
		}
		
		wp_enqueue_script( 'wp-super-edit-tinymcescan', '/wp-includes/js/tinymce/tiny_mce_config.php?wp_super_edit_tinymce_scan=scan', false, rand(101, 199) );
		
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
	global $wp_super_edit;
?>

	<link rel="stylesheet" href="<?php echo $wp_super_edit->core_uri ?>css/wp_super_edit.css" type="text/css" />

<?php
}

/**
* Uninstall plugin
*
* Function used when to clear settings.
*
*/
function superedit_uninstall() {
	delete_option('superedit_options');
	delete_option('superedit_buttons');
	delete_option('superedit_plugins');
}

/**
* Deactivate plugin
*
* Function used to redirect to Plugin Administration Panel and deactivate plugin.
*
*/
function superedit_deactivate() {
    $url = add_query_arg( '_wpnonce', wp_create_nonce( 'deactivate-plugin_wp-super-edit/wp-super-edit.php' ), 'plugins.php?action=deactivate&plugin=wp-super-edit/wp-super-edit.php' );
	wp_redirect( $url );
}




function wp_super_edit_register_defaults() {

	$wp_super_edit_registry = new wp_super_edit_registry();
	$tinymce_scan = $wp_super_edit_registry->get_option( 'tinymce_scan' );
	
	print_r( $tinymce_scan );
	
}

/**
* Set user configurations from
*
* Uses array_walk to set status of plugins or buttons.
*
* @param $settings Define the array of settings to work with.
* @param $name The plugin or button to work with.
* @param $postvalue Value array from $_POST.
*/
function superedit_postvalues ( &$settings, $name, $type ) {

	global $superedit_ini;

	if ( isset( $_POST[$type][$name] ) ) {	
		$settings['status'] = attribute_escape( $_POST[$type][$name]  );
	} else {
		$settings['status'] = 'N';
	}
	
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
* Display common options: Language.
*
* Allows changes to Language options.
*
* @global $superedit_ini
*/
function wp_super_edit_options_ui() {
	global $superedit_ini;
?>
<div id="superedit_options">
	<fieldset class="options">
		<legend>WP Super Edit Options</legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr valign="top">
					<th width="45%" scope="row">Use English as the default language</th>
					<td width="5%" style="background: #ccc;"><input name="superedit_language" type="checkbox" value="Y" <?php if ($superedit_ini['options']['language'] == 'EN' ) { echo 'checked="checked"' ;} ?> /></td>
					<td width="60%" scope="row">
					<p>
					The Wordpress visual editor does have international language support, but language files 
					for the plugins may need to be installed manually.
					</p>
					<p>
					Currently this plugin only ships with English language files to make the archive smaller. Please <a href="http://factory.funroe.net/projects/wp-super-edit/using/">see the WP Super Edit Custom Language Documentation</a>
					for more information.
					</p>
					</td>
				</tr>
			</table>
	</fieldset>
</div>
<?php

	wp_super_edit_deactivate_ui();

}

/**
* Get status for a plugin or button.
*
* Takes an option name and type and returns the status value
*
* @param $name Plugin or button name.
* @param $type Define if you are checking a plugin or button.
* @global array $superedit_ini
*/
function superedit_option_status( $name, $type ) {
	global $superedit_ini;
	return $superedit_ini[$type][$name]['status'];
}

/**
* Set the row and postion for a button.
*
* Takes an button name, row, and postion and sets those options to be saved.
*
* @param $button Button name.
* @param $position Location of button in row.
* @param $row Row location of button.
* @global array $superedit_buttons
*/
function superedit_position_buttons ( $button, $position, $row ) {
	global $superedit_ini;
	$superedit_ini['buttons'][$button]['row'] = $row;
	$superedit_ini['buttons'][$button]['position'] = $position + 1;
}

/**
* Set the separator option for buttons.
*
* Takes button separator settings sets those options to be saved.
*
* @param $settings Button settings from array.
* @param $name Button name.
*/
function superedit_set_separator ( &$settings, $name ) {
	if ( isset( $_POST['separators'][$name] ) ) {	
		$settings['separator'] = attribute_escape( $_POST['separators'][$name]  );
	} else {
		$settings['separator'] = 'N';
	}
}

/**
* Build tables for plugins and buttons with javascript functions
*
* Uses array_walk to build table, rows, and cells for either buttons or plugins.
* Includes javascript functions for drag drop button interface
*
* @param $title Title for fieldset block.
* @param $option Array for button or plugin.
* @param $superedit_settings Users currently set settings.
* @param $value Defines the name for the checkbox.
* @param $description Optional description for fieldset block.
*/
function superedit_layout_html ( $title, $option, $value ) {
?>
<div id="superedit_<?php echo $value; ?>">

	<fieldset class="options">
		<legend><?php echo $title; ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">

		<?php foreach ( $option as $name => $settings ) : ?>
			<tr valign="top">
				<th width="45%" scope="row"><?php echo $settings['desc']; ?></th>
				<td width="5%" style="background: #ccc;"><input id="<?php echo $name; ?>" name="<?php echo $value.'['.$name.']'; ?>" type="checkbox" value="Y" <?php if ($settings['status'] == 'Y' ) { echo 'checked="checked"' ;} ?> /></td>
				<td width="60%" scope="row"><?php echo $settings['notice']; ?></td>
			</tr>
		<?php endforeach; ?>

			</table>
	</fieldset>
</div>
<?php
}

/**
* Creates hidden form elements
*
* Designed to create hidden form elements for separators and buttons.
*
* @param $settings Button settings from array.
* @param $name Button name.
* @param $type Defines hidden element as button or separator.
*/
function superedit_form_hidden ( $settings, $name, $type ) {
	if ( $name != '' ) {
		if ( $type[0] == 'buttons' ) {
			$status = $settings['status'];
		} elseif ( $type[0] == 'separators' ) {
			$status = $settings['separator'];
		}
?>
			<input type="hidden" id="<?php echo $type[1].$name; ?>" name="<?php echo $type[0].'['.$name.']'; ?>" value="<?php echo $status; ?>" />
<?php
	}
}

/**
* Creates HTML elements for arranging buttons
*
* Creates elements used in sortable interface to arrange buttons by row and position
*
* @param $name Button name.
* @param $position Unused.
* @global array $superedit_ini 
*/
function superedit_layout_buttons ( $name, $position ) {
	global $superedit_ini;
	
	$plugin = $superedit_ini['buttons'][$name]['plugin'];
	
	if ( $name != '' ) {
?>
			<?php if (!$plugin || $superedit_ini['plugins'][$plugin]['status'] == 'Y' ) : ?>
			<div id="<?php echo $name; ?>" class="lineitem<?php if ( $superedit_ini['buttons'][$name]['separator'] == 'Y' ) echo ' button_separator'; ?>"><div class="button_info"><img onclick="getButtonInfo('<?php echo $name; ?>');" src="<?php echo $wp_super_edit->core_uri ?>images/info.png" width="14" height="16" alt="Button Info" title="Button Info" /><img onclick="toggleSeparator('<?php echo $name; ?>');" src="<?php echo $wp_super_edit->core_uri ?>images/separator.png" width="14" height="7" alt="Toggle Separator" title="Toggle Separator" /></div> <?php echo $superedit_ini['buttons'][$name]['desc']; ?></div>
			<?php endif; ?>

<?php
	}
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
function superedit_jobjects ( $settings, $name, $type ) {
	global $superedit_ini;
	if ( $type == 'buttons' ) {	
		printf("\t\ttiny_mce_buttons['%s'] = new superedit_button( '%s', '%s', '%s', '%s', '%s', '%s', '%s' );\n", $name, $settings['desc'], $settings['notice'], $superedit_ini['buttons'][$name]['status'], $superedit_ini['buttons'][$name]['row'], $superedit_ini['buttons'][$name]['position'], $settings['separator'], $settings['plugin'] );
	} else {
		printf("\t\ttiny_mce_plugins['%s'] = new superedit_plugin( '%s', '%s', '%s' );\n", $name, $settings['desc'], $settings['notice'], $superedit_ini['plugins'][$name]['status'] );
	}
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
	global $wp_super_edit, $wp_super_edit_admin, $superedit_ini;
		
	$updated = false;
	
	if ( !$wp_super_edit_admin->is_installed && $_REQUEST['wp_super_edit_action'] != 'install' ) {
		echo 'NOT INSTALLED';
		$wp_super_edit_admin->install_ui();
		return;
	}

	if (  $_REQUEST['wp_super_edit_action'] == 'uninstall' ) {
		superedit_uninstall();
		superedit_deactivate();
	}
	
	if (  $_REQUEST['wp_super_edit_action'] == 'install' ) {
		check_admin_referer( 'wp_super_edit_nonce-' . $wp_super_edit_admin->nonce );

		include_once( $wp_super_edit->core_path . 'wp-super-edit-defaults.php');
		wp_super_edit_install_db_tables();
		wp_super_edit_wordpress_button_defaults();
		wp_super_edit_plugin_folder_scan();
	}	
	
	if ( $_REQUEST['wp_super_edit_action'] == 'settings' ) {
	
		if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(__('Security test failed'));
		
		check_admin_referer( 'wp_super_edit_nonce-' . $wp_super_edit_admin->nonce );

		if (  $_REQUEST['wp_super_edit_ui'] == 'buttons' ) {
			$row_order_1 = explode( ',', $_POST['order_row_1'] );
			$row_order_2 = explode( ',', $_POST['order_row_2'] );
			$row_order_3 = explode( ',', $_POST['order_row_3'] );
			
			array_walk( $row_order_1, 'superedit_position_buttons', 1 );
			array_walk( $row_order_2, 'superedit_position_buttons', 2 );
			array_walk( $row_order_3, 'superedit_position_buttons', 3 );
	
			array_walk( $superedit_ini['buttons'], 'superedit_postvalues', 'buttons' );
			
			array_walk( $superedit_ini['buttons'], 'superedit_set_separator' );
		}
		
		if (  $_REQUEST['wp_super_edit_ui'] == 'plugins' ) {
			array_walk( $superedit_ini['plugins'], 'superedit_postvalues', 'plugins' );
		}

		if (  $_REQUEST['wp_super_edit_ui'] == 'options' ) {
			$superedit_ini['options']['language'] = ( $_POST['superedit_language'] == 'Y' ? 'EN' : 'NO' );
		}
		
		$superedit_savesettings = superedit_usersettings( $superedit_ini );
		
		update_option('superedit_options',$superedit_ini['options']);
		update_option('superedit_buttons',$superedit_ini['buttons']);
		update_option('superedit_plugins',$superedit_ini['plugins']);
		
		$updated = true;

	}
	
	// User Notification
	if ($updated) superedit_update_message();
		
	// Construct Button containers
	$buttonrow1 = array();
	$buttonrow2 = array();
	$buttonrow3 = array();
	$buttonrowdefault = array();

	foreach ( $superedit_ini['buttons'] as $name => $button_options ) {
	
		if ( $button_options['row'] == 1 && $button_options['status'] == 'Y' ) {
			$buttonrow1[$button_options['position']] = $name;
		} elseif ( $button_options['row'] == 2 && $button_options['status'] == 'Y' ) {
			$buttonrow2[$button_options['position']] = $name;
		} elseif ( $button_options['row'] == 3 && $button_options['status'] == 'Y' ) {
			$buttonrow3[$button_options['position']] = $name;
		} else {
			$buttonrowdefault[] = $name;
		}
		
	}

	ksort( $buttonrow1 );
	ksort( $buttonrow2 );
	ksort( $buttonrow3 );
	ksort( $buttonrowdefault );
	

		
	// Plugin options form
	?>

			<div class="wrap">


		
				<h2>WP Super Edit</h2>
					
				<p>
				To give you more control over the Wordpress TinyMCE WYSIWYG Visual Editor. For more information please vist the <a href="http://factory.funroe.net/projects/wp-super-edit/">WP Super Edit project.</a>
				</p>
				
				<?php $wp_super_edit_admin->admin_menu_ui() ?>

<?php 

echo $wp_super_edit_admin->html_tag ( array(
	'class' => 'tester_class',
	'id' => 'tester_id',
	'align' => 'right',
	'content' => 'Test Text',
	'tag' => 'p',
	'return' => true
) );

echo $wp_super_edit_admin->html_tag ( array(
	'class' => 'tester_class',
	'id' => 'tester_id',
	'value' => 'right',
	'content' => 'Test Text',
	'tag' => 'input',
	'tag_type' => 'single',
	'return' => true
) );

?>
		<pre>
		<?php
		
		print_r( $wp_super_edit );
		
		wp_super_edit_register_defaults();
		
		?>
		</pre>
		
				<?php $wp_super_edit_admin->form_start(); ?>
			
				<?php $wp_super_edit_admin->submit_button(); ?>

		<?php if ( !$_GET['wp_super_edit_ui'] || $_GET['wp_super_edit_ui'] == 'buttons' ) : ?>

			<input type="hidden" name="wp_super_edit_action" value="buttons" />
			
			<input type="hidden" id="i_wp_super_edit_row_1" name="wp_super_edit_row_1" value="" />
			<input type="hidden" id="i_wp_super_edit_row_2" name="wp_super_edit_row_2" value="" />
			<input type="hidden" id="i_wp_super_edit_row_3" name="wp_super_edit_row_3" value="" />
			<input type="hidden" id="i_wp_super_edit_row_4" name="wp_super_edit_row_4" value="" />

			
			<?php array_walk( $superedit_ini['buttons'], 'superedit_form_hidden', array( 'buttons', 'bval_' ) );?>
			<?php array_walk( $superedit_ini['buttons'], 'superedit_form_hidden', array( 'separators', 'sval_' ) );?>
								
				
				<div id="button_tab">
					<fieldset class="options">
		
						<legend>Arrange Editor Buttons</legend>
					
						<div id="button_rows">
							
							<div class="row_container disabled_buttons">
								<h3>Disabled Buttons</h3>
								<div id="tinymce_buttons" class="section">
									<?php array_walk( $buttonrowdefault, 'superedit_layout_buttons' );?>
								</div>
							</div>
						
							<div class="row_container">
								<h3>Editor Button Row 1</h3>
								<div id="row1" class="section">
									<?php array_walk( $buttonrow1, 'superedit_layout_buttons' );?>
								</div>
							</div>
						
							<div class="row_container">
								<h3>Editor Button Row 2</h3>
								<div id="row2" class="section">
									<?php array_walk( $buttonrow2, 'superedit_layout_buttons' );?>
								</div>
							</div>
							
							<div class="row_container">
								<h3>Editor Button Row 3</h3>
								<div id="row3" class="section">
									<?php array_walk( $buttonrow3, 'superedit_layout_buttons' );?>
								</div>
							</div>
							
							<br class="clearer" />
						</div>
										
					</fieldset>
				</div>		
		<?php endif; ?>
		
		<?php if ( $_GET['wp_super_edit_ui'] == 'plugins' ) : ?>	
				<input type="hidden" name="wp_super_edit_action" value="plugins" />

				<div id="plugins_tab">
					<?php superedit_layout_html( 'TinyMCE Plugins', $superedit_ini['plugins'], 'plugins' ); ?>	
				</div>
		<?php endif; ?>
		
		<?php if ( $_GET['wp_super_edit_ui'] == 'options' ) : ?>	
				<input type="hidden" name="wp_super_edit_action" value="options" />

				<div id="options_tab">			
					<?php wp_super_edit_options_ui(); ?>
				</div>
		<?php endif; ?>
				
				<?php $wp_super_edit_admin->submit_button(); ?>
				
				<?php $wp_super_edit_admin->form_end(); ?>
				
				<?php $wp_super_edit_admin->uninstall_ui(); ?>

		
			</div>

<?php 
}




function superedit_options_footer() {
?>
<script type="text/javascript">
	// <![CDATA[
	
	// ]]>
</script> 
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
	global $superedit_ini;

?>

<script type="text/javascript">
	// <![CDATA[

	// Define custom jQuery namespace to keep away javascript conflicts
	var wpsuperedit = jQuery.noConflict();

	// Default Variables and Objects
		
	function superedit_button( desc, notice, status, row, position, separator, plugin ) {
		this.desc = desc;
		this.notice = notice;
		this.status = status;
		this.row = row;    		
		this.position = position;
		this.separator = separator;    
		this.plugin = plugin;
	  }
	
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
			wpsuperedit('#row1').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
				}
			);
	
			wpsuperedit('#row2').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
				}
			);		
			
			wpsuperedit('#row3').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
				}
			);		
			
			wpsuperedit('#tinymce_buttons').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
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