<?php
/*
Plugin Name: WP Super Edit
Plugin URI: http://www.funroe.net/projects/superedit/
Description: Get some control over the visual/wysiwyg editor and add some functionality without modifying the Wordpress source code.
Author: Jesse Planck
Version: 1.1.2
Author URI: http://www.funroe.net/

Copyright (c) 2007 Jess Planck (http://www.funroe.net/)
WP Super Edit is released under the GNU General Public
License: http://www.gnu.org/licenses/gpl.txt

This is a WordPress plugin (http://wordpress.org). WordPress is
free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free
Software Foundation; either version 2 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

For a copy of the GNU General Public License, write to:

Free Software Foundation, Inc.
59 Temple Place, Suite 330
Boston, MA  02111-1307
USA

You can also view a copy of the HTML version of the GNU General
Public License at http://www.gnu.org/copyleft/gpl.html
*/

/**
* WP Super Edit 
*
* These functions control the core functionality for this Wordpress Plugin. This
* plugin is designed to control the Wordpress visual WYSIWYG editor. The editor
* is a javascript application known as TinyMCE provided by Moxicode AB. 
*
* @package superedit
*
*/

/**
* array_intersect_key function for php4 compatibility
*
* This is a function to be used inplace of the builtin array_intersect_key for 
* PHP5. This function should respond the same for php4 users.
*
* @global $wp_version
*/
if ( !function_exists('array_intersect_key' ) ) {
	function array_intersect_key( $isec, $keys ) {
		$argc = func_num_args();
		if ( $argc > 2 ) {
			for ( $i = 1; !empty( $isec ) && $i < $argc; $i++ ) {
				$arr = func_get_arg( $i );
				foreach ( array_keys( $isec ) as $key) {
					if ( !isset( $arr[$key] ) ) {
						unset( $isec[$key] );
					}
				}
			}
			return $isec;
		} else {
			$res = array();
			foreach ( array_keys( $isec ) as $key ) {
				if ( isset( $keys[$key] ) ) {
					$res[$key] = $isec[$key];
				}
			}
			return $res;
		}
	}
}

/**
* Check compatiblity.
*
* Checks to make sure that this plugin is compatible with installed version of
* Wordpress.
*
* @global $wp_version
*/
function superedit_compatibility_check() {
	global $wp_version;
	if ($wp_version >= '2.1' ) {
		return true;
	} else {
		return false;
	}
}

/**
* Compatiblity notice.
*
* Displays a prominent notice for incompatible sites. Notice is needed to make 
* sure that users understand requirements for this plugin.
*
* @global $wp_version
*/
function superedit_compatibility_notice() {
	global $wp_version;
?>
<div style="width: 100%; padding: 8px; margin: 8px; font-size: 120%; font-weight: bold; background: #FFFF66; border: 2px dashed #FF0000;"> 
	<p style="text-align: center; font-size: 130%;">I'm really sorry!</p>
	
	<p>WP Super Edit cannot support your current version of Wordpress ( <?php echo $wp_version; ?> ) 
	You should immediately deactivate the WP Super Edit plugin. </p>
	
	<p>Please use <a href="http://www.wordpress.org">Wordpress</a> version 2.2 or higher! Although this plugin may work with some other versions
	of Wordpress, I urge you to be aware of security issues that may arise using older versions of Wordpress.</p>
</div>
<?php
}

/**
* Load settings
*
* Loads settings from PHP INI style configuration files stored in this plugin
* directory and in the individual TinyMCE plugin directories.
*
*/
function superedit_loadsettings() {

	$superedit_ini['options'] = parse_ini_file( ABSPATH.'wp-content/plugins/superedit/superedit_config.php' );
	$superedit_ini['buttons'] = parse_ini_file( ABSPATH.'wp-content/plugins/superedit/superedit_builtin.php', true );	

	$tinymce_plugins_loc = ABSPATH.'wp-content/plugins/superedit/tinymce_plugins/';
	$tinymce_plugins = @ dir($tinymce_plugins_loc);
	while(($tinymce_plugin = $tinymce_plugins->read()) !== false) {
		if ( is_dir( $tinymce_plugins_loc.'/'.$tinymce_plugin ) && is_readable( $tinymce_plugins_loc.'/'.$tinymce_plugin ) ) {
			if ( $tinymce_plugin{0} == '.' || $tinymce_plugin == '..' ) {
				continue;
			}
			$tinymce_plugin_dir = @ dir($tinymce_plugins_loc . '/' . $tinymce_plugin);
			while (($tinymce_plugin_config = $tinymce_plugin_dir->read()) !== false) {
				if ( $tinymce_plugin_config == 'config.php' ) {
					$plugin_ini =  parse_ini_file( $tinymce_plugins_loc . $tinymce_plugin . '/' . $tinymce_plugin_config, true );

					foreach ( $plugin_ini as $option => $settings ) {
						if ( is_array( $plugin_ini[$option] ) ) {
							$superedit_ini['buttons'][$option] = $settings;
						} else {
							$superedit_ini['plugins'][$tinymce_plugin][$option] = $settings;
						}
					}
					
					break;
				}
			}
		}
	}
	
	return $superedit_ini;
}

/**
* Create user settings
*
* This creates a simpler array from the parsed configuration array and is used 
* for the actual user settings stored in the database.
*
*/
function superedit_usersettings( $superedit_ini = array() ) {
	$returnusersettings['options'] = $superedit_ini['options'];
	$button_template = array (
		'status' => '',
		'row' => 0,
		'position' => 0,
		'separator' => '');
	$plugin_template = array (
		'status' => '',
		'callbacks' => '');
	foreach ( $superedit_ini['buttons'] as $name => $button ) {
		$returnusersettings['buttons'][$name] = array_intersect_key( $button, $button_template );
	}
	foreach ( $superedit_ini['plugins'] as $name => $plugin ) {
		$returnusersettings['plugins'][$name] = array_intersect_key( $plugin, $plugin_template );
	} 	
	return $returnusersettings;
}

/**
* Activate plugin
*
* Function used when this plugin is activated in Wordpress.
*
*/
function superedit_activate() {
	if ( superedit_compatibility_check() ) {
		$superedit_ini = superedit_loadsettings();
		$superedit_current = superedit_usersettings($superedit_ini);
		add_option('superedit_options', $superedit_current['options']);
		add_option('superedit_buttons', $superedit_current['buttons']);
		add_option('superedit_plugins', $superedit_current['plugins']);
	} else {
		superedit_compatibility_notice();
	}
}

/**
* Deactivate plugin
*
* Function used when this plugin is deactivated in Wordpress.
*
*/
function superedit_deactivate() {
	delete_option('superedit_options');
	delete_option('superedit_buttons');
	delete_option('superedit_plugins');
}

/**
* Set up administration interface
*
* Function used by Wordpress to initialize the adminsitrative interface.
*
* @global array $superedit_ini
* @global array $superedit_options
* @global array $superedit_buttons
* @global array $superedit_plugins
*/
function superedit_admin_setup() {
	global $superedit_ini, $superedit_options, $superedit_buttons, $superedit_plugins;

	$page = preg_replace('!^.*[\\\\/]wp-content[\\\\/][^\\\\/]*plugins[\\\\/]!', '', __FILE__);
	$page = str_replace('\\', '/', $page);
	
	add_submenu_page('plugins.php', __('WP Super Edit', 'superedit'), __('WP Super Edit', 'superedit'), 5, $page, 'superedit_admin_page');
	
	if ( $_GET['page'] == $page ) {

		require_once('superedit_admin.php');

		$superedit_ini = superedit_loadsettings();
		$superedit_options = get_option('superedit_options');
		$superedit_buttons = get_option('superedit_buttons');

		foreach ( $superedit_buttons as $bname => $button_options ) {
			if ( is_array( $superedit_ini['buttons'][$bname] ) ) {
				$superedit_ini['buttons'][$bname]['status'] = $button_options['status'];
				$superedit_ini['buttons'][$bname]['row'] = $button_options['row'];
				$superedit_ini['buttons'][$bname]['position'] = $button_options['position'];
				$superedit_ini['buttons'][$bname]['separator'] = $button_options['separator'];
			}
		}
		
		foreach ( $superedit_plugins as $pname => $plugin_options ) {
			if ( is_array( $superedit_ini['plugins'][$pname] ) ) {
				$superedit_ini['plugins'][$pname]['status'] = $plugin_options['status'];
				$superedit_ini['plugins'][$pname]['callbacks'] = $plugin_options['callbacks'];
			}
		}
		
		$superedit_ini['options']['language'] = $superedit_options['language'];
						
		if (function_exists('wp_enqueue_script')) {
		
			wp_deregister_script( 'prototype' );
			wp_deregister_script( 'jquery' );
			wp_deregister_script( 'interface' );

		
			wp_enqueue_script( 'superedit-jquery',  '/wp-content/plugins/superedit/js/jquery.pack.js', false, '2135' );
			wp_enqueue_script( 'superedit-greybox',  '/wp-content/plugins/superedit/js/greybox.js', false, '2135' );
			wp_enqueue_script( 'superedit-history',  '/wp-content/plugins/superedit/js/jquery.history_remote.pack.js', false, '2135' );
			wp_enqueue_script( 'superedit-tabs',  '/wp-content/plugins/superedit/js/jquery.tabs.pack.js', false, '2135' );

		}
		
		add_action('admin_head', 'superedit_admin_head');
		add_action('admin_footer', 'superedit_admin_footer');

		do_action('superedit_admin_setup');
	}
}

/**
* Language filter
*
* Wordpress filter used to set the default language on the editor to English if
* necessary.
*
* @param $locale Wordpress provided language setting.
*/
function superedit_locale($locale) {
	global $superedit_options, $superedit_plugins, $superedit_buttons;
	
	if (strstr($_SERVER['REQUEST_URI'], 'tiny_mce_config')) {
		$superedit_buttons = get_option('superedit_buttons');
		$superedit_options = get_option('superedit_options');
				
		if ( $superedit_options['language'] == 'EN' ) {
			$locale = 'EN';
		}
	}
	
	return $locale; 
}

/**
* Map buttons.
*
* Helper function used map buttons to row and position.
*
* @param $row TinyMCE button row.
* @param $oldbuttons Wordpress buttons for row.
* @global array $superedit_buttons
*/
function superedit_map_buttons($row, $oldbuttons) {
	global $superedit_buttons;
	
	$separators = array();
	
	foreach ( $superedit_buttons as $name => $button ) {
		$key = array_search( $name, $oldbuttons );
		if ( $key !== false ) {
			unset( $oldbuttons[$key] );
			if (  $oldbuttons[$key+1] == 'separator' ) unset( $oldbuttons[$key+1] );
		}	
			
		if ( $button['row'] == $row && $button['status'] == 'Y' ) {
			if ($button['separator'] == 'Y') $separators[] = $button['position'];
			$buttons[$button['position']] = $name;
		}	
	}
	
	if ( is_array( $buttons ) ) {
		sort( $separators );
		ksort( $buttons );
		krsort( $oldbuttons );	

		$separatoroffset = 0;

		foreach ( $separators as $location ) {
			array_splice( $buttons, $location + $separatoroffset, 0, 'separator' );
			$separatoroffset++;
		}		
	
		foreach ( $oldbuttons as $old => $oldbutton ) {
			array_unshift( $buttons, $oldbutton );
		}
			
		return $buttons;
	} else return $oldbuttons;
}

/**
* TinyMCE Plugin URLs
*
* Function used by Wordpress to set up TinyMCE plugin URLs provided by this 
* application and set by the user.
*
* @global array $superedit_plugins
*/
function superedit_external_plugins() {
	global $superedit_plugins;
	
	foreach ( $superedit_plugins as $name => $plugin ) {
		if ( $plugin['status'] == 'Y' ) {
		    echo 'tinyMCE.loadPlugin("'.$name.'", "'.get_option('siteurl').'/wp-content/plugins/superedit/tinymce_plugins/'.$name.'/");'."\n"; 
		} 
	}
   
	return;
}

/**
* TinyMCE Plugins
*
* Function used by Wordpress to set up TinyMCE plugins provided by this 
* application and set by the user.
*
* @param $plugins Wordpress provided TinyMCE plugins.
* @global array $superedit_plugins
*/
function superedit_mce_plugins($plugins) {
	global $superedit_plugins;
	
	foreach ( $superedit_plugins as $name => $plugin ) {
		if ( $plugin['status'] == 'Y' ) {
			$key = array_search( $name, $plugins );
			if ( $key ) {
				$plugins[$key] = "-$name";
			} else {
				array_push($plugins, "-$name");
			}
		}
	}
	
    return $plugins;
}

/**
* TinyMCE row 1 buttons
*
* Function used by Wordpress to set up TinyMCE buttons for row 1 in the editor.
* Buttons are provided by this application and Wordpress. These can set by 
* manipulating options in the WP Super Edit administration interface.
*
* @param $buttons Wordpress provided TinyMCE buttons for row 1.
* @global array $superedit_buttons
*/
function superedit_mce_buttons_1($buttons) {
	global $superedit_buttons;
	
	$buttons = superedit_map_buttons(1, $buttons);
	
	if (empty($buttons)) {
		return array();
	} else {
		return $buttons; 
	}
}

/**
* TinyMCE row 2 buttons
*
* Function used by Wordpress to set up TinyMCE buttons for row 2 in the editor.
* Buttons are provided by this application and Wordpress. These can set by 
* manipulating options in the WP Super Edit administration interface.
*
* @param $buttons Wordpress provided TinyMCE buttons for row 2.
* @global array $superedit_buttons
*/
function superedit_mce_buttons_2($buttons) {
	global $superedit_buttons;
	
	$buttons = superedit_map_buttons(2, $buttons);
	
	if (empty($buttons)) {
		return array();
	} else {
		return $buttons; 
	}
}

/**
* TinyMCE row 3 buttons
*
* Function used by Wordpress to set up TinyMCE buttons for row 3 in the editor.
* Buttons are provided by this application and Wordpress. These can set by 
* manipulating options in the WP Super Edit administration interface.
*
* @param $buttons Wordpress provided TinyMCE buttons for row 3.
* @global array $superedit_buttons
*/
function superedit_mce_buttons_3($buttons) {
	global $superedit_buttons;

	$buttons = superedit_map_buttons(3, $buttons);
	
	if (empty($buttons)) {
		return array();
	} else {
		return $buttons; 
	}
}

/**
* Superedit Initialization
*
* This function used by Wordpress to initialize this application. Some of the 
* TinyMCE plugins used in WP Super Edit have callback functions that can be run
* if the editor plugin is activated.
*
* @global array $superedit_options
* @global array $superedit_buttons
* @global array $superedit_plugins
*/
function superedit_init() {
	global $superedit_plugins;
	
	if ( superedit_compatibility_check() ) {

		load_plugin_textdomain('superedit', 'wp-content/plugins/superedit');
		add_action('admin_menu', 'superedit_admin_setup');
		
		// Plugin Callback functions
		$superedit_plugins = get_option('superedit_plugins');
		
		if ( is_array( $superedit_plugins ) ) {
			foreach ( $superedit_plugins as $name => $plugin ) {
				if ( isset( $plugin['callbacks'] ) && $plugin['callbacks'] != "" && $plugin['status'] == 'Y') {
					$superedit_callbacks = explode(',', $plugin['callbacks']);
					
					$tinymce_plugins_loc = ABSPATH.'wp-content/plugins/superedit/tinymce_plugins/';
					require( $tinymce_plugins_loc.$name.'/functions.php');
					
					foreach ( $superedit_callbacks as $callback => $command ) {
						call_user_func(trim($command));
					}
					
				}
			}
		}
		
	}  else {
		superedit_compatibility_notice();
	}
	
	do_action('superedit_init');
}

/**
* Define Global Variables
*/
$superedit_ini = array();
$superedit_options = array();
$superedit_buttons = array();
$superedit_plugins = array();

/**
* Start security checks
*/
if ( !function_exists('wp_nonce_field') ) {
        function superedit_nonce_field($action = -1) { return; }
        $superedit_nonce = -1;
} else {
        function superedit_nonce_field($action = -1) { return wp_nonce_field($action); }
        $superedit_nonce = 'superedit-update-key';
}

/**
* Define Wordpress actions and filters
*/
if (isset($wp_version)) {
    add_filter('mce_plugins', 'superedit_mce_plugins', 11);
    add_filter('mce_buttons', 'superedit_mce_buttons_1', 11);    
    add_filter('mce_buttons_2', 'superedit_mce_buttons_2', 11);
    add_filter('mce_buttons_3', 'superedit_mce_buttons_3', 11);
    // Language Check
    add_filter('locale', 'superedit_locale');
    add_action('tinymce_before_init','superedit_external_plugins');
    add_action('activate_superedit/superedit.php','superedit_activate');
    add_action('deactivate_superedit/superedit.php','superedit_deactivate');
}

add_action('init', 'superedit_init', 5);
?>