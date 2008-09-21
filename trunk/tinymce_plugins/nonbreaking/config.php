<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'nonbreaking', 
	'nicename' => __('Nonbreaking Spaces'), 
	'description' => __('Adds button to insert nonbreaking space entity.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'nonbreaking', 
	'nicename' => __('Nonbreaking Space'), 
	'description' => __('Inserts nonbreaking space entities.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'nonbreaking', 
	'status' => 'no'
));


?>