<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'emotions', 
	'nicename' => 'Custom Emoticon / Icon Plugin', 
	'description' => 'Customized Wordpress Emoticon / Icon images. Uses Wordpress icon set. Provides the Emoticon / Icons Button.', 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'superemotions', 
	'nicename' => 'Emoticon / Icons', 
	'description' => 'Interface for Customized Wordpress Emoticon / Icon images. Uses Wordpress icon set.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'emotions', 
	'status' => 'no'
));


?>