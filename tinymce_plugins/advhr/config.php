<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'advhr', 
	'nicename' => __('Advanced Rule Lines'), 
	'description' => __('Advanced rule lines with options for &lt;hr&gt; HTML tag.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'advhr', 
	'nicename' => __('Advanced Rule Lines'), 
	'description' => __('Options for using the &lt;hr&gt; HTML tag'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'advhr', 
	'status' => 'no'
));


?>