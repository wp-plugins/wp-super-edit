<?php
/**
* WP Super Edit Plugin Callback Function file
*
* This is a plugin callback function file for WP Super Edit. This allows
* the addition of callback functions for each plugin added to WP Super Edit.
*/

if ( !function_exists('superedit_custom_editor_css') ) {
// Should always check for function incase we have multiple callbacks

	function superedit_custom_css($mce_css) {
		$mce_css .= ',' . get_bloginfo('stylesheet_directory') . '/editor.css';
		return $mce_css; 
	}

	function superedit_custom_editor_css() {
		add_filter('mce_css', 'superedit_custom_css');
	}
}

?>