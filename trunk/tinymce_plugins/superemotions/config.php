<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'superemotions', 
	'nicename' => __('Super Emoticon / Icon Plugin'), 
	'description' => __('Wordpress Emoticon / Icon images. Uses Wordpress icon set. Provides the Emoticon / Icons Button. Uses WordPress shortcodes API.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => 'superemotions_add_shortcode'
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'superemotions', 
	'nicename' => __('Super Emoticon / Icons'), 
	'description' => __('Interface for Wordpress Emoticon / Icon images. Uses Wordpress icon set. Uses WordPress shortcodes API.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'superemotions', 
	'status' => 'no'
));


?>