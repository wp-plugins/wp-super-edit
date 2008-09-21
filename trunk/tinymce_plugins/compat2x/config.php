<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'compat2x', 
	'nicename' => __('TinyMCE 2.x Compatiblity'), 
	'description' => __('This plugin attempts to offer compatibility with old TinyMCE 2.x plugins. Please suggest to the author to upgrade development to TinyMCE 3.x'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));


?>