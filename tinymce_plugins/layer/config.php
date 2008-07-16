<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'layer', 
	'nicename' => 'Layers (DIV) Plugin', 
	'description' => 'Insert layers using DIV HTML tag. This plugin will change the editor to allow all DIV tags. Provides the Insert Layer, Move Layer Forward, Move Layer Backward, and Toggle Layer Positioning Buttons.', 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => 'superedit_allow_div'
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'insertlayer', 
	'nicename' => 'Insert Layer', 
	'description' => 'Insert a layer using the DIV HTML tag. Be careful layers are tricky to position.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'moveforward', 
	'nicename' => 'Move Layer Forward', 
	'description' => 'Move selected layer forward in stacked view.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'movebackward', 
	'nicename' => 'Move Layer Backward', 
	'description' => 'Move selected layer backward in stacked view.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'absolute', 
	'nicename' => 'Toggle Layer Positioning', 
	'description' => 'Toggle the layer positioning as absolute or relative. Be careful layers are tricky to position.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'layer', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

?>