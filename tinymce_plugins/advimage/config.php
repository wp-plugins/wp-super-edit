<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'advimage', 
	'nicename' => __('Advanced Image Link'), 
	'description' => __('A more advanded dialog for the Image Link button.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));



?>