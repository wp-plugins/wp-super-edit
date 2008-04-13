<?php
/*
Plugin Name: WP Super Edit
Plugin URI: http://factory.funroe.net/projects/wp-super-edit/
Description: Get some control over the visual/wysiwyg editor and add some functionality without modifying the Wordpress source code.
Author: Jesse Planck
Version: 2.0
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
* plugin is designed to extend and control the Wordpress visual WYSIWYG editor. The editor
* is a javascript application known as TinyMCE provided by Moxicode AB. 
*
* @package wp-super-edit
*
*/

/**
* @internal: WP Super Edit core class always needed
*/
require_once( ABSPATH .  PLUGINDIR . '/wp-super-edit/wp-super-edit.core.class.php' );

/**
* wp_super_edit primary object instance
*/
$wp_super_edit = new wp_super_edit_core();

/**
* @internal: Conditional includes for WP Super Edit fuctions and classes in WordPress admin panels
*/
if ( is_admin() ) {
	require_once( ABSPATH . PLUGINDIR . '/wp-super-edit/wp-super-edit.admin.class.php' );
	require_once( ABSPATH . PLUGINDIR . '/wp-super-edit/wp-super-edit-defaults.php' );
	require_once( ABSPATH . PLUGINDIR . '/wp-super-edit/wp-super-edit-admin.php' );
} 


/**
* WP Super Edit Initialization
*
* This function used by Wordpress to initialize this application. Some TinyMCE
* plugins used in WP Super Edit may have callback functions that need to run
*
*/
function wp_super_edit_init() {
	global $wp_super_edit;
	
	if ( !$wp_super_edit->is_db_installed ) return;
	
	$plugin_callbacks = $wp_super_edit->plugin_init();
			
	if ( !$plugin_callbacks ) return;
		
	foreach ( $plugin_callbacks as $number => $plugin ) {
		$callbacks = explode( ',', $plugin->callbacks );
		
		require_once( $wp_super_edit_init->tinymce_plugins_path . $plugin->name . '/functions.php' );
		
		foreach ( $callbacks as $callback => $command ) {
			call_user_func( trim( $command ) );
		}
			
	}

}

/**
* TinyMCE before init filter
*
* This function is a filter used by Wordpress when the TinyMCE javascript is accessed. This
* filter performs scans of TinyMCE settings ( hopefully to get info about other plugins ), and 
* this filter performs the work of modifying the TinyMCE editor.
*
*/
function wp_super_edit_tiny_mce_before_init( $initArray ) {
	$wp_super_edit_db = new wp_super_edit_db();
	
	if ( !$wp_super_edit_db->is_db_installed ) return;

	$tinymce_scan_current_time = time();
	$tinymce_scan_last_time = $wp_super_edit_db->get_option( 'tinymce_scan_last_time' );

	if ( $_GET['wp_super_edit_tinymce_scan'] == 'scan' ||  $tinymce_scan_current_time > $tinymce_scan_last_time + 3600 ) {
		
		$wp_super_edit_db->set_option( 'tinymce_scan_last_time', $tinymce_scan_current_time );
		$wp_super_edit_db->set_option( 'tinymce_scan', $initArray );

		$initArray['disk_cache'] = false;
		$initArray['compress'] = false;
		$initArray['wp_super_edit_update_marker'] = true;
		
		return $initArray;
	}
	
}


/**
* Start security checks
*/
if ( !function_exists('wp_nonce_field') ) {
        function wp_super_edit_nonce_field($action = -1) { return; }
        $wp_super_edit_nonce = -1;
} else {
        function wp_super_edit_nonce_field($action = -1) { return wp_nonce_field($action); }
        $wp_super_edit_nonce = 'wp-super-edit-update-key';
}

/**
* @internal: Define core Wordpress actions and filters
*/

//load_plugin_textdomain('wp-super-edit', 'wp-content/plugins/wp-super-edit');
	
add_action('init', 'wp_super_edit_init', 5);
add_filter('tiny_mce_before_init','wp_super_edit_tiny_mce_before_init', 99);    

/**
* @internal: Conditional activation for WP Super Edit interfaces in WordPress admin panels
*/
if ( is_admin() ) {
	add_action('admin_init', 'wp_super_edit_admin_setup');
} 

    
?>
