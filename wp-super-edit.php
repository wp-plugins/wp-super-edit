<?php
/*
Plugin Name: WP Super Edit
Plugin URI: http://factory.funroe.net/projects/wp-super-edit/
Description: Get some control over the visual/wysiwyg editor and add some functionality without modifying the Wordpress source code.
Author: Jesse Planck
Version: 2.0
Author URI: http://funroe.net

Copyright (c) 2007 Jess Planck (http://funroe.net)
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
* Conditional includes for WP Super Edit fuctions and classes in WordPress admin panels
* Set $wp_super_edit primary object instance
*/
if ( is_admin() ) {
	require_once( ABSPATH . PLUGINDIR . '/wp-super-edit/wp-super-edit.admin.class.php' );
	require_once( ABSPATH . PLUGINDIR . '/wp-super-edit/wp-super-edit-admin.php' );
	$wp_super_edit = new wp_super_edit_admin();
} else {
	$wp_super_edit = new wp_super_edit_core();
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
	
	if ( !$wp_super_edit->is_installed ) return;
	
	$plugin_callbacks = $wp_super_edit->plugin_init();
			
	if ( !$plugin_callbacks ) return;
		
	foreach ( $plugin_callbacks as $number => $plugin ) {
		$callbacks = explode( ',', $plugin->callbacks );
		
		require_once( $wp_super_edit->tinymce_plugins_path . $plugin->name . '/functions.php' );
		
		foreach ( $callbacks as $callback => $command ) {
			call_user_func( trim( $command ) );
		}
			
	}

}

function wp_super_edit_tinymce_filter( $initArray ) {
	global $wp_super_edit;

	if ( $_REQUEST['scan'] == 'wp_super_edit_tinymce_scan' ) {

		if ( !$wp_super_edit->is_installed ) {
			add_option( 'wp_super_edit_tinymce_scan', $initArray );
		} else {
			$wp_super_edit->set_option( 'tinymce_scan', $initArray );	
		}

		unset( $initArray['disk_cache'] );
		unset( $initArray['compress'] );
	}
	
	return $initArray;
}

/**
* @internal: Define core Wordpress actions and filters
*/

//load_plugin_textdomain('wp-super-edit', 'wp-content/plugins/wp-super-edit');

add_action('init', 'wp_super_edit_init', 5);

if ( strpos( $_SERVER['SCRIPT_FILENAME'], 'tiny_mce_config.php' ) !== false ) {
	add_action('mce_options', 'wp_super_edit_mce_options', 5);
	add_filter('tiny_mce_before_init','wp_super_edit_tinymce_filter', 99);
}


/**
* @internal: Conditional activation for WP Super Edit interfaces in WordPress admin panels
*/
if ( is_admin() ) {
	add_action('admin_init', 'wp_super_edit_admin_setup');
} 
    
?>
