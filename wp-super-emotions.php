<?php
/*
Plugin Name: WP Super Edit WordPress Emoticons
Plugin URI: http://funroe.net/projects/super-edit/
Description: Adds emoticon / icon button to editor that uses default installed WordPress icon set.
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
* WP Super Class function to register items in WP Super Edit
* Use $wp_super_edit primary object instance to add settings to database
* @global object $wp_super_edit 
*/
function wp_super_emoticons_register() {
	global $wp_super_edit;
	
	// WP Super Edit options for this plugin
	
	$wp_super_edit->register_tinymce_plugin( array(
		'name' => 'wp-super-emoticons', 
		'nicename' => __('Super Emoticon / Icon Plugin'), 
		'description' => __('Wordpress Emoticon / Icon images. Uses Wordpress icon set. Provides the Emoticon / Icons Button. Uses WordPress shortcodes API.'), 
		'provider' => 'wp_super_edit', 
		'status' => 'no', 
		'callbacks' => 'wp_super_emoticons_add_shortcode'
	));
	
	// Tiny MCE Buttons provided by this plugin
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'superemotions', 
		'nicename' => __('Super Emoticon / Icons'), 
		'description' => __('Interface for Wordpress Emoticon / Icon images. Uses Wordpress icon set. Uses WordPress shortcodes API.'), 
		'provider' => 'wp_super_edit', 
		'plugin' => 'wp-super-emoticons', 
		'status' => 'no'
	));
}
add_action('wp_super_edit_loaded', 'wp_super_emoticons_register', 5);

/**
* WP Super Emoticons to add shortcode to WordPress editor
*/
function wp_super_emoticons_add_shortcode() {
	add_shortcode('superemotions', 'wp_super_emoticons_shortcode');
}

/**
* WP Super Emoticons to add shortcode to WordPress editor
*/
function wp_super_emoticons_shortcode ($attr, $content = null ) {
	$attr = shortcode_atts(array(
		'file'   => 'file',
		'title'    => 'title'
		), $attr);
								 
	return '<img class="superemotions" title="' . $attr['title'] . '" alt="'  . $attr['title'] . '" border="0" src="' . get_bloginfo('wpurl') . '/wp-includes/images/smilies/' . $attr['file'] . '" />';
}



?>