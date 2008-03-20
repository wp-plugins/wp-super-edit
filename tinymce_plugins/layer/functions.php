<?php
/**
* WP Super Edit Plugin Callback Function file
*
* This is a plugin callback function file for WP Super Edit. This allows
* the addition of callback functions for each plugin added to WP Super Edit.
*/

if ( !function_exists('superedit_allow_div') ) {
// Should always check for function incase we have multiple callbacks

	function editor_allow_div($valid_elements) {
		return str_replace('p/-div[*],', '', $valid_elements);
	}

	function superedit_allow_div() {
		add_filter('mce_valid_elements', 'editor_allow_div', 11);
	}
}

?>