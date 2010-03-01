<?php
/*
Plugin Name: WP Super Edit
Plugin URI: http://funroe.net/projects/super-edit/
Description: Get control of the WordPress wysiwyg visual editor and add some functionality with more buttons and customized TinyMCE plugins.
Author: Jess Planck
Version: 2.2.1
Author URI: http://funroe.net

Copyright (c) Jess Planck (http://funroe.net)
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
* @package wp-super-edit
* @author Jess Planck
* @version 2.2
*/

/**
* WP Super Edit core variables defined
*/
define( WPSE_VERSION, '2.3' );

/**
* WP Super Edit core class always required
*/
require_once( WP_PLUGIN_DIR . '/wp-super-edit/wp-super-edit.core.class.php' );

/**
* Filter to set init of the wp_super_edit_core class with basic functionality
*/
$wp_super_edit_run_mode = 'off';

/**
* Conditional includes for WP Super Edit fuctions and classes in WordPress admin panels
* Set $wp_super_edit primary object instance
* @global object $wp_super_edit 
*/
if ( is_admin() ) {
	require_once( WP_PLUGIN_DIR . '/wp-super-edit/wp-super-edit.admin.class.php' );
	require_once( WP_PLUGIN_DIR . '/wp-super-edit/wp-super-edit-admin.php' );
	$wp_super_edit_run_mode = 'admin';
}

/**
* Conditional include for WP Super Edit installation fuctions
* Set $wp_super_edit primary object instance
* @global object $wp_super_edit 
*/
if (  $_REQUEST['wp_super_edit_action'] == 'install' ) {
	include_once( $wp_super_edit->core_path . 'wp-super-edit-defaults.php');
}

$wp_super_edit_run_mode = apply_filters( 'wp_super_edit_run_mode',  $wp_super_edit_run_mode );

/**
* @internal: Conditional activation for WP Super Edit interfaces
*/
switch( $wp_super_edit_run_mode ) {
	// Minimal WP Super Edit usage
	case 'core':
		$wp_super_edit = new wp_super_edit_core();
		add_action('init', 'wp_super_edit_init', 5);
		break;
	// WP Super Edit Administration interfaces and default manipulation of TinyMCE.
	case 'admin':
		$wp_super_edit = new wp_super_edit_admin();
		load_plugin_textdomain( 'wp-super-edit', WP_PLUGIN_DIR . '/' .dirname(plugin_basename(__FILE__)) . '/languages', dirname(plugin_basename(__FILE__)) . '/languages' );
		
		add_action('init', 'wp_super_edit_init', 5);
		add_action('admin_menu', 'wp_super_edit_admin_menu_setup');
		add_action('admin_init', 'wp_super_edit_admin_setup');		
		add_filter('mce_external_plugins','wp_super_edit_tinymce_plugin_filter', 99);
		add_filter('tiny_mce_before_init','wp_super_edit_tinymce_filter', 99);

}

/**
* WP Super Edit Initialization
*
* This function used by Wordpress to initialize this application. Some TinyMCE
* plugins used in WP Super Edit may have callback functions that need to run
* @global object $wp_super_edit 
*/
function wp_super_edit_init() {
	global $wp_super_edit;
	
	if ( !$wp_super_edit->is_installed ) return;
							
	foreach ( $wp_super_edit->plugins as $plugin_name => $plugin ) {
			
		if ( $plugin->status == 'no' ) continue;
		
		if ( strlen( $plugin->callbacks ) < 2 ) continue;
				
		$callbacks = explode( ',', $plugin->callbacks );
		
		foreach ( $callbacks as $callback => $command ) {
			if ( !function_exists( $command ) ) continue;
			call_user_func( trim( $command ) );
		}
			
	}

}

/**
* WP Super Edit TinyMCE filter
*
* This function is a WordPress filter designed to replace the TinyMCE configuration array
* with the configuration array created by WP Super Edit.
* @global object $wp_super_edit 
*/
function wp_super_edit_tinymce_filter( $initArray ) {
	global $wp_super_edit;

	if ( !$wp_super_edit->is_installed ) return $initArray;

	$initArray = $wp_super_edit->tinymce_settings( $initArray );
	
	return $initArray;
}

/**
* WP Super Edit TinyMCE Plugin filter
*
* This WordPress filter passes plugins activated by WP Super Edit and passes them during init of 
* TinyMCE.
* @global object $wp_super_edit 
*/
function wp_super_edit_tinymce_plugin_filter( $tinymce_plugins ) {
	global $wp_super_edit;
		
	if ( !$wp_super_edit->is_installed ) return $tinymce_plugins;
	
	if ( !is_array( $wp_super_edit->plugins ) ) return;
	
	foreach( $wp_super_edit->plugins as $plugin ) {
		if ( $plugin->status != 'yes' ) continue;
		if ( $plugin->provider == 'tinymce' ) continue;

		if ( $plugin->url != '' ) {
			if ( preg_match("/^(http:|https:)/i", $plugin->url ) ) {
				$tinymce_plugins[$plugin->name] = $plugin->url;
			} else {
				$tinymce_plugins[$plugin->name] = $wp_super_edit->tinymce_plugins_uri . $plugin->name . $plugin->url;
			}
		} else { 
			$tinymce_plugins[$plugin->name] = $wp_super_edit->tinymce_plugins_uri . $plugin->name . '/editor_plugin.js';
		}
	}
	
	return $tinymce_plugins;
}

do_action( 'wp_super_edit_loaded', 'wp_super_edit_loaded' );

print_r($wp_super_edit);

?>