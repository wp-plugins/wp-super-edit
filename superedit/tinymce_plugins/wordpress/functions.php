<?php
/**
* WP Super Edit Plugin Callback Function file
*
* This is a plugin callback function file for WP Super Edit. This allows
* the addition of callback functions for each plugin added to WP Super Edit.
*/

if ( !function_exists('superedit_custom_editor_wpcss') ) {
// Should always check for function incase we have multiple callbacks

	function superedit_custom_wpcss($mce_css) {
	
		$pattern = '(htt\w+/wordpress.css)';
		$replacement = get_bloginfo('wpurl') . '/wp-content/plugins/superedit/tinymce_plugins/wordpress/wordpress.css';
		$mce_css = eregi_replace($pattern, $replacement, $mce_css);
	
		return $mce_css; 
	}

	function superedit_custom_editor_wpcss() {
		add_filter('mce_css', 'superedit_custom_wpcss');
	}
}

if ( !function_exists('superedit_disable_wpautop') ) {
// Should always check for function incase we have multiple callbacks

	function superedit_disable_wpautop() {
		remove_filter('the_content', 'wpautop');
	}
	
}

?>