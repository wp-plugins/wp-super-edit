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
* Set user configurations from
*
* Uses array_walk to set status of plugins or buttons.
*
* @param $settings Define the array of settings to work with.
* @param $name The plugin or button to work with.
* @param $postvalue Value array from $_POST.
*/
function superedit_postvalues ( &$settings, $name, $type ) {
	if ( isset( $_POST[$type][$name] ) ) {	
		$settings['status'] = attribute_escape( $_POST[$type][$name]  );
	} else {
		$settings['status'] = 'N';
	}
}

/**
* Display title and basic WP Super Edit information.
*
* Just to display the title and basic information about the plugin.
*/
function superedit_admin_title() {
?>
	<h2>WP Super Edit</h2>
	<p style="padding: 6px; font-size: 98%;">To give you more control over the Wordpress TinyMCE WYSIWYG Visual Editor. For more information please vist the <a href="http://www.funroe.net/projects/superedit/">WP Super Edit project.</a></p>
<?php
}

/**
* Display Update Options button for interface forms
*
* Just to display the form button for the WP Super Edit admin interfaces.
*/
function superedit_submit_button() {
?>
	<p class="submit clearer">
		<input type="submit" name="update_superedit" value="Update Options &raquo;" />
	</p>
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
		<p><?php printf(__('WP Super Edit Settings Updated. Remember... Reload your editor or empty your cache and <a href="%s">Go Write Something!!</a> &raquo;'), get_option('siteurl') . $writepost . '?up='. rand(101, 199) ); ?></p>
		<p>
		<span style="color:red;">In most cases you will need to RELOAD the editor page, in some extreme cases you may need to EMPTY YOUR BROWSER CACHE before your new options will be available.</span> 
		</p>
	</div>       
<?php
}

/**
* Display common options: Language.
*
* Allows changes to Language options.
*
* @global $superedit_options
*/
function superedit_options_html() {
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
					Currently this plugin only ships with English language files to make the archive smaller. Please <a href="http://www.funroe.net/projects/superedit/">see the WP Super Edit Custom Language Documentation</a>
					for more information.
					</p>
					</td>
				</tr>
			</table>
	</fieldset>
</div>
<?php
}

/**
* Get status for a plugin or button.
*
* Takes an option name and type and returns the status value
*
* @param $name Plugin or button name.
* @param $type Define if you are checking a plugin or button.
* @global array $superedit_buttons
* @global array $superedit_plugins
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
				<td width="5%" style="background: #ccc;"><input id="<?php echo $name; ?>" name="<?php echo $value.'['.$name.']'; ?>" onclick="toggleButtons(this)" type="checkbox" value="Y" <?php if ($settings['status'] == 'Y' ) { echo 'checked="checked"' ;} ?> /></td>
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
* @global array $superedit_buttons
* @global array $superedit_plugins
*/
function superedit_layout_buttons ( $name, $position ) {
	global $superedit_ini;
	
	$plugin = $superedit_ini['buttons'][$name]['plugin'];
	
	if ( $name != '' ) {
?>
			<?php if (!$plugin || $superedit_ini['plugins'][$plugin]['status'] == 'Y' ) : ?>
			<div id="<?php echo $name; ?>" class="lineitem<?php if ( $superedit_ini['buttons'][$name]['separator'] == 'Y' ) echo ' button_separator'; ?>"><div class="button_info"><img onclick="getButtonInfo('<?php echo $name; ?>');" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/images/info.png" width="14" height="16" alt="Button Info" title="Button Info" /><img onclick="toggleSeparator('<?php echo $name; ?>');" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/images/separator.png" width="14" height="7" alt="Toggle Separator" title="Toggle Separator" /></div> <?php echo $superedit_ini['buttons'][$name]['desc']; ?></div>
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
* @global array $superedit_buttons
* @global array $superedit_plugins
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
* Add javascript and css to the HEAD area
*
* Some complex CSS and javascript functions to operate the WP Super Edit advanced interface.
*
* @global array $superedit_ini 
* @global array $superedit_options
* @global array $superedit_buttons
* @global array $superedit_plugins
*/
function superedit_admin_head() {
	global $superedit_ini;

?>
<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/js/superedit.js?up=<?php echo rand(101, 199); ?>"></script>
<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/tinymce/tiny_mce_config.php?up=<?php echo rand(101, 199); ?>"></script>

<link rel="stylesheet" href="<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/css/superedit.css" type="text/css" />

<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/css/tabs_ie.css" type="text/css" media="projection, screen" />
<![endif]-->

<script type="text/javascript">
	// <![CDATA[
	
	/*
	Default Variables and Objects
	*/
	
	function superedit_button( desc, notice, status, row, position, separator, plugin ) {
		this.desc = desc;
		this.notice = notice;
		this.status = status;
		this.row = row;    		
		this.position = position;
		this.separator = separator;    
		this.plugin = plugin;
	  }
	
	function superedit_plugin( desc, notice, status ) {
		this.desc = desc;
		this.notice = notice;
		this.status = status;
	  }
	
	var data;
	var tiny_mce_buttons = new Object();
	var tiny_mce_plugins = new Object();
	var buttons = new Array();
	
<?php array_walk( $superedit_ini['buttons'], 'superedit_jobjects', 'buttons' );?>

<?php array_walk( $superedit_ini['plugins'], 'superedit_jobjects', 'plugins' );?>

	
	/*
	Plugin and Button Control Functions
	*/

	function makeButton(button) {
		var separator = '';
		if ( $('#sval_' + button ).attr('value') == 'Y' ) separator = ' button_separator';
		$('#tinymce_buttons').append('<div id="' + button + '" class="lineitem' + separator + '"><div class="button_info"><img onclick="getButtonInfo(' + "'" + button + "'" + ');" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/images/info.png" title="Button Info" alt="Button Info" width="14" height="16" /><img onclick="toggleSeparator(' + "'" + button + "'" + ');" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/images/separator.png" width="14" height="7" alt="Toggle Separator" title="Toggle Separator" /></div>' + tiny_mce_buttons[button].desc + '</div>').SortableAddItem($('#' + button)[0]);
		return;
	}
	
	function addItem(plugin) {
	  $.each( tiny_mce_buttons, function(i, n) {

	  	if ( n.plugin == plugin ) {
	        makeButton( i );		
		}

	  });
	}
	
	function removeItem(plugin) {
	  $.each( tiny_mce_buttons, function(i, n) {

	  	if ( n.plugin == plugin ) {
			$( '#' + i ).remove();
		
		}

	  });
	}
	
	function toggleButtons(plugin) {
		if ( plugin.checked ) {
			addItem(plugin.id);
		} else {
			removeItem(plugin.id)		
		}
	}
	
	function toggleSeparator(button) {
		$( '#' + button ).toggleClass( 'button_separator' );
		
		if ( $('#sval_' + button ).attr('value') == 'Y' ) {
			$( '#sval_' + button ).attr('value','N');
		} else {
			$( '#sval_' + button ).attr('value','Y');
		}
		
	}

	function getButtonInfo(button) {
	
		$.GB_show('about:blank', {
				close_img: "<?php echo get_option('siteurl'); ?>/wp-content/plugins/superedit/images/close.gif",
				height: 280,
				width: 300,
				animation: true,
				overlay_clickable: true,
				caption: 'Editor Button Information'
		  });
		 // We don't want the iframe greybox gives us: 
		$('#GB_frame').remove();
		$("#GB_window").append("<div id='GB_frame'></div>");
		$("#GB_frame").append("<h3>" + tiny_mce_buttons[button].desc + "</h3><p>" + tiny_mce_buttons[button].notice + "</p>");

		return false;		
	}	
	
	// ]]>
</script> 
<?php
	do_action('superedit_admin_head');
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
function superedit_admin_page() {
	global $superedit_ini;
		
	$updated = false;
		
	if (isset($_POST['update_superedit'])) {
	
		if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(__('Security test failed'));
		check_admin_referer( '$superedit_nonce', $superedit_nonce );

		$row_order_1 = explode( ',', $_POST['order_row_1'] );
		$row_order_2 = explode( ',', $_POST['order_row_2'] );
		$row_order_3 = explode( ',', $_POST['order_row_3'] );
		
		array_walk( $row_order_1, 'superedit_position_buttons', 1 );
		array_walk( $row_order_2, 'superedit_position_buttons', 2 );
		array_walk( $row_order_3, 'superedit_position_buttons', 3 );

		array_walk( $superedit_ini['buttons'], 'superedit_postvalues', 'buttons' );

		array_walk( $superedit_ini['plugins'], 'superedit_postvalues', 'plugins' );
		
		array_walk( $superedit_ini['buttons'], 'superedit_set_separator' );

		$superedit_savesettings = superedit_usersettings( $superedit_ini );

		$superedit_ini['options']['language'] = ( $_POST['superedit_language'] == 'Y' ? 'EN' : 'NO' );
		
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
	
		<form id="tinymce_controller" enctype="application/x-www-form-urlencoded" action="<?php echo htmlspecialchars( $_SERVER['REQUEST_URI'] ); ?>" method="post">
			<?php superedit_nonce_field('$superedit_nonce', $superedit_nonce); ?>

			<input type="hidden" name="superedit_action" value="update" />
			
			<input type="hidden" id="o_row_1" name="order_row_1" value="" />
			<input type="hidden" id="o_row_2" name="order_row_2" value="" />
			<input type="hidden" id="o_row_3" name="order_row_3" value="" />
			
			<?php array_walk( $superedit_ini['buttons'], 'superedit_form_hidden', array( 'buttons', 'bval_' ) );?>
			<?php array_walk( $superedit_ini['buttons'], 'superedit_form_hidden', array( 'separators', 'sval_' ) );?>
			
			<?php superedit_admin_title(); ?>
			
			<?php superedit_submit_button(); ?>	
			
			
			<div id="superedit_tabs">
		
				<ul>
					<li><a href="#button_tab"><span>Arrange Editor Buttons</span></a></li>
					<li><a href="#plugins_tab"><span>Configure Editor Plugins</span></a></li>
					<li><a href="#options_tab"><span>Super Edit Options</span></a></li>
				</ul>
				
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
	
				<div id="plugins_tab">
					<?php superedit_layout_html( 'TinyMCE Plugins', $superedit_ini['plugins'], 'plugins' ); ?>	
				</div>
				
				<div id="options_tab">			
					<?php superedit_options_html(); ?>
				</div>
				
			</div>
				
				<?php superedit_submit_button(); ?>
				
		</form>
		
</div>

<script type="text/javascript">
	// <![CDATA[
	$(document).ready(
		function() {

			// Set up tab interface
			$('#superedit_tabs').tabs();

			// Controls Drag + Drop
			$('#row1').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
				}
			);
	
			$('#row2').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
				}
			);		
			
			$('#row3').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
				}
			);		
			
			$('#tinymce_buttons').Sortable(
				{
					accept: 'lineitem',
					helperclass: 'sort_placeholder',
					opacity: 0.7,
					tolerance: 'pointer'
				}
			);

			// Set up values for form submission
			$('#tinymce_controller').submit(
				function() {
				serial2 = $.SortSerialize();
				
				
				$.each( serial2.o.tinymce_buttons, function(i, n){
				  $( '#bval_' + n ).attr('value','N');			  
				});			
	
				$.each( serial2.o.row1, function(i, n){
				  $('#bval_' + n ).attr('value','Y');
				  $( '#o_row_1' ).attr('value', serial2.o.row1 );
				  
				});
				
				$.each( serial2.o.row2, function(i, n){
				  $('#bval_' + n ).attr('value','Y');
				  $( '#o_row_2' ).attr('value', serial2.o.row2 );
	
				});
				
				$.each( serial2.o.row3, function(i, n){
				  $('#bval_' + n ).attr('value','Y');
				  $( '#o_row_3' ).attr('value', serial2.o.row3 );
	
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