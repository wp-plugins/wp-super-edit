<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'superclass', 
	'nicename' => __('Custom CSS Classes'), 
	'description' => __('Adds Custom styles button and CLASSES from an editor.css file in your <strong>Currently active THEME</strong> directory. Provides the Custom CSS Classes Button.'), 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => 'superedit_custom_editor_css'
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit->register_tinymce_button( array(
	'name' => 'styleselect', 
	'nicename' => __('Custom CSS Classes'), 
	'description' => __('Shows a drop down list of CSS Classes that the editor has access to.'), 
	'provider' => 'wp_super_edit', 
	'plugin' => 'superclass', 
	'status' => 'no'
));


?>
