<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'table', 
	'nicename' => __('Tables Plugin'), 
	'description' => __('Allows the creation and manipulation of tables using the TABLE HTML tag. Provides the Tables and Table Controls Buttons.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'table', 
	'nicename' => __('Tables'), 
	'description' => __('Interface to create and change table, row, and cell properties.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'table', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'tablecontrols', 
	'nicename' => __('Table controls'), 
	'description' => __('Interface to manipulate tables and access to cell and row properties.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'table', 
	'status' => 'no'
));

?>