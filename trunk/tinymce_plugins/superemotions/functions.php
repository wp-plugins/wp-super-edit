<?php
/**
* WP Super Edit Plugin Callback Function file
*
* This is a plugin callback function file for WP Super Edit. This allows
* the addition of callback functions for each plugin added to WP Super Edit.
*/

if ( !function_exists('superemotions_add_shortcode') ) {
// Should always check for function incase we have multiple callbacks
	
	function superemotions_shortcode ($attr, $content = null ) {
		$attr = shortcode_atts(array(
			'file'   => 'file',
			'title'    => 'title'
			), $attr);
									 
		return '<img class="superemotions" title="' . $attr['title'] . '" alt="'  . $attr['title'] . '" border="0" src="' . get_bloginfo('wpurl') . '/wp-includes/images/smilies/' . $attr['file'] . '" />';
	}

	function superemotions_add_shortcode() {
		add_shortcode('superemotions', 'superemotions_shortcode');
	}
	
}

?>