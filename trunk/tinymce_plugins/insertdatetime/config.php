<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'insertdatetime', 
	'nicename' => __('Insert Date / Time Plugin'), 
	'description' => __('Adds insert date and time buttons to automatically insert date and time.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'insertdate', 
	'nicename' => __('Insert Date'), 
	'description' => __('Insert current date in editor'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'insertdatetime', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'inserttime', 
	'nicename' => __('Insert Time'), 
	'description' => __('Insert current time in editor'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'insertdatetime', 
	'status' => 'no'
));

?>