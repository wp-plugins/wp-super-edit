<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'searchreplace', 
	'nicename' => __('Search and Replace Plugin'), 
	'description' => __('Adds search and replace buttons and options to the editor.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'search', 
	'nicename' => __('Search'), 
	'description' => __('Search for text in editor area.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'searchreplace', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'replace', 
	'nicename' => __('Replace'), 
	'description' => __('Replace text in editor area.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'searchreplace', 
	'status' => 'no'
));

?>