<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'print', 
	'nicename' => 'Print Button Plugin', 
	'description' => 'Adds print button to editor that should print only the edit area contents.', 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'print', 
	'nicename' => 'Print', 
	'description' => 'Print editor area contents.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'print', 
	'status' => 'no'
));


?>