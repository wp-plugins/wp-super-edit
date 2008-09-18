<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit->register_tinymce_plugin( array(
	'name' => 'contextmenu', 
	'nicename' => 'Context Menu', 
	'description' => 'TinyMCE context menu is used by some plugins. The context menu is activated by right mouse click or crtl click on Mac in the editor area.', 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));


?>