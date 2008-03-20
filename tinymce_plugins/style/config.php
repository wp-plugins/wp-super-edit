<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit_registry->register_tinymce_plugin( array(
	'name' => 'style', 
	'nicename' => 'Advanced CSS / styles Plugin', 
	'description' => 'Allows access to properties that can be used in a STYLE attribute. Provides the Style Properties Button.', 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit_registry->register_tinymce_button( array(
	'name' => 'superstyleprops', 
	'nicename' => 'Style Properties', 
	'description' => 'Interface for properties that can be manipulated using the STYLE attribute.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'style', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));


?>