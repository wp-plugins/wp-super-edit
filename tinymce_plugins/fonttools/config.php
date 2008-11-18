<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'fonttools', 
	'nicename' => __('Font Tools'), 
	'description' => __('Adds the Font Family and Font Size buttons to the editor.'), 
	'provider' => 'tinymce', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'fontselect', 
	'nicename' => __('Font Select'), 
	'description' => __('Shows a drop down list of Font Typefaces.'), 
	'provider' => 'tinymce', 
	'plugin' => 'fonttools', 
	'status' => 'no'
));

$wp_super_edit->register_tinymce_button( array(
	'name' => 'fontsizeselect', 
	'nicename' => __('Font Size Select'), 
	'description' => __('Shows a drop down list of Font Sizes.'), 
	'provider' => 'tinymce', 
	'plugin' => 'fonttools', 
	'status' => 'no'
));

?>