<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'style', 
	'nicename' => 'Advanced CSS / styles Plugin', 
	'description' => 'Allows access to properties that can be used in a STYLE attribute. Provides the Style Properties Button.', 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'styleprops', 
	'nicename' => 'Style Properties', 
	'description' => 'Interface for properties that can be manipulated using the STYLE attribute.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'style', 
	'status' => 'no'
));


?>