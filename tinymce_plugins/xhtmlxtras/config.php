<?php
/*

 WP Super Edit Plugin Configuration file

 This is a plugin configuration file for WP Super Edit.
 Each TinyMCE plugin added to WP Super Edit should have similar options.

*/

// WP Super Edit options for this plugin

$wp_super_edit_registry->register_tinymce_plugin( array(
	'name' => 'xhtmlextras', 
	'nicename' => 'XHTML Extras Plugin', 
	'description' => 'Allows access to interfaces for some XHTML tags like CITE, ABBR, ACRONYM, DEL and INS. Also can give access to advanced XHTML properties such as javascript events. Provides the Citation, Abbreviation, Acronym, Deletion, Insertion, and XHTML Attributes Buttons.', 
	'provider' => 'wp_super_edit', 
	'status' => 'no', 
	'callbacks' => ''
));

// Tiny MCE Buttons provided by this plugin

$wp_super_edit_registry->register_tinymce_button( array(
	'name' => 'cite', 
	'nicename' => 'Citation', 
	'description' => 'Indicate a citation using the HTML CITE tag.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlextras', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit_registry->register_tinymce_button( array(
	'name' => 'abbr', 
	'nicename' => 'Abbreviation', 
	'description' => 'Indicate an abbreviation using the HTML ABBR tag.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlextras', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit_registry->register_tinymce_button( array(
	'name' => 'acronym', 
	'nicename' => 'Acronym', 
	'description' => 'Indicate an acronym using the HTML ACRONYM tag.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlextras', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit_registry->register_tinymce_button( array(
	'name' => 'del', 
	'nicename' => 'Deletion', 
	'description' => 'Use the HTML DEL tag to indicate recently deleted content.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlextras', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit_registry->register_tinymce_button( array(
	'name' => 'ins', 
	'nicename' => 'Insertion', 
	'description' => 'Use the HTML INS tag to indicate newly inserted content.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlextras', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

$wp_super_edit_registry->register_tinymce_button( array(
	'name' => 'attribs', 
	'nicename' => 'XHTML Attributes', 
	'description' => 'Modify advanced attributes and javascript events.', 
	'provider' => 'wp_super_edit', 
	'plugin' => 'xhtmlextras', 
	'status' => 'no', 
	'separator' => 'no', 
	'row' => 0, 
	'position' => 0
));

?>