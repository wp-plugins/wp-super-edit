<?php


/**
* WP Super Edit Install Database Tables
*
* Installs default database tables for WP Super Edit.
*
*/
function wp_super_edit_install_db_tables() {
	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$wp_super_edit = new wp_super_edit_core();

	if ( $wp_super_edit->is_db_installed ) return;

	if ( $wpdb->supports_collation() ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}

	$install_sql="CREATE TABLE $wp_super_edit->db_options (
	 id bigint(20) NOT NULL auto_increment,
	 name varchar(60) NOT NULL default '',
	 value text NOT NULL,
	 PRIMARY KEY (id,name),
	 UNIQUE KEY name (name)
	) $charset_collate;
	CREATE TABLE $wp_super_edit->db_plugins (
	 id bigint(20) NOT NULL auto_increment,
	 name varchar(60) NOT NULL default '',
	 url text NOT NULL,
	 nicename varchar(120) NOT NULL default '',
	 description text NOT NULL,
	 provider varchar(60) NOT NULL default '',
	 status varchar(20) NOT NULL default 'no',
	 callbacks varchar(120) NOT NULL default '',
	 PRIMARY KEY (id,name),
	 UNIQUE KEY name (name)
	) $charset_collate;
	CREATE TABLE $wp_super_edit->db_buttons (
	 id bigint(20) NOT NULL auto_increment,
	 name varchar(60) NOT NULL default '',
	 nicename varchar(120) NOT NULL default '',
	 description text NOT NULL default '',
	 provider varchar(60) NOT NULL default '',
	 plugin varchar(60) NOT NULL default '',
	 status varchar(20) NOT NULL default 'no',
	 button_separator varchar(10) NOT NULL default 'no',
	 row tinyint UNSIGNED NOT NULL default '0',
	 position tinyint UNSIGNED NOT NULL default '0',
	 PRIMARY KEY (id,name),
	 UNIQUE KEY id (id)
	) $charset_collate;
	CREATE TABLE $wp_super_edit->db_users (
	 id bigint(20) NOT NULL auto_increment,
	 user_id bigint(20) NOT NULL default '0',
	 user_name varchar(60) NOT NULL default '',
	 user_type text NOT NULL default '',
	 editor_options text NOT NULL,
	 PRIMARY KEY (id,user_name),
	 UNIQUE KEY id (id)
	) $charset_collate;";
	
	dbDelta($install_sql);
		
}



/**
* WP Super Edit WordPress Button Defaults
*
* Registers known default TinyMCE buttons included in default WordPress installation
*
*/
function wp_super_edit_wordpress_button_defaults() {
	global $wpdb, $wp_super_edit;

	$wp_super_edit_registry = new wp_super_edit_registry();

	if ( !$wp_super_edit_registry->is_db_installed ) return;

	$wp_super_edit_registry->get_registered();
		
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'bold', 
		'nicename' => 'Bold', 
		'description' => 'Bold content with strong HTML tag. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 1
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'italic', 
		'nicename' => 'Italic', 
		'description' => 'Italicize content with em HTML tag. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 2
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'strikethrough', 
		'nicename' => 'Strikethrough', 
		'description' => 'Strike out content with strike HTML tag. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 3
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'bullist', 
		'nicename' => 'Bulleted List', 
		'description' => 'An unordered list. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 4
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'numlist', 
		'nicename' => 'Numbered List', 
		'description' => 'An ordered list. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 5
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'outdent', 
		'nicename' => 'Decrease Indentation', 
		'description' => 'This will decrease the level of indentation based on content position. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 6
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'indent', 
		'nicename' => 'Increase Indentation', 
		'description' => 'This will increase the level of indentation based on content position. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 7
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'justifyleft', 
		'nicename' => 'Left Justification', 
		'description' => 'Set the alignment to left justification. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 8
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'justifycenter', 
		'nicename' => 'Center Justification', 
		'description' => 'Set the alignment to center justification. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 9
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'justifyright', 
		'nicename' => 'Right Justification', 
		'description' => 'Set the alignment to right justification. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 10
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'link', 
		'nicename' => 'Create Link', 
		'description' => 'Create a link. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 11
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'unlink', 
		'nicename' => 'Remove Link', 
		'description' => 'Remove a link. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 12
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'image', 
		'nicename' => 'Image Link', 
		'description' => 'Insert linked image. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 13
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'wp_more', 
		'nicename' => 'Wordpress More Tag', 
		'description' => 'Insert Wordpress MORE tag to divide content to multiple views. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 14
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'spellchecker', 
		'nicename' => 'Spell Check', 
		'description' => 'Wordpress spell check. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 15
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'wp_help', 
		'nicename' => 'Wordpress Help', 
		'description' => 'Built in Wordpress help documentation. Wordpress default editor option for first row.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 16
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'wp_adv_start', 
		'nicename' => 'START - Advanced toolbar', 
		'description' => '<strong>This is a place holder, not a true button.</strong> Used as the START point for FIRST ROW buttons that you want in the <strong>Advanced toolbar</strong>. Note these buttons may appear hidden see Advanced Toolbar Button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 17
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'wp_adv', 
		'nicename' => 'Show/Hide Advanced toolbar', 
		'description' => 'Built in Wordpress button <strong>normally hidden</strong>. When pressed it will show an extra row of buttons (or press Ctrl-Alt-V on FF, Alt-V on IE). Uses the <strong>START - Advanced toolbar</strong> and <strong>END - Advanced toolbar</strong> as markers for hidden buttons.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 18
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'formatselect', 
		'nicename' => 'Paragraphs and Headings', 
		'description' => 'Set Paragraph or Headings for content.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 19
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'underline', 
		'nicename' => 'Underline Text', 
		'description' => 'Built in Wordpress button to underline selected text.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 20
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'justifyfull', 
		'nicename' => 'Full Justification', 
		'description' => 'Set the alignment to full justification. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 21
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'forecolor', 
		'nicename' => 'Foreground color', 
		'description' => 'Set foreground or text color. May produce evil font tags. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 22
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'pastetext', 
		'nicename' => 'Paste as Text', 
		'description' => 'Paste clipboard text and remove formatting. Useful for pasting text from applications that produce substandard HTML. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 23
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'pasteword', 
		'nicename' => 'Paste from Microsoft Word', 
		'description' => 'Attempts to clean up HTML produced by Microsoft Word during cut and paste. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 24
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'removeformat', 
		'nicename' => 'Remove HTML Formatting', 
		'description' => 'Removes HTML formatting from selected item. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 25
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'cleanup', 
		'nicename' => 'Clean up HTML', 
		'description' => 'Attempts to clean up bad HTML in the editor. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 26
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'charmap', 
		'nicename' => 'Special Characters', 
		'description' => 'Insert special characters or entities using a visual interface. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'yes', 
		'row' => 1, 
		'position' => 27
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'undo', 
		'nicename' => 'Undo option', 
		'description' => 'Undo previous formatting changes. Not useful once you save. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 28
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'redo', 
		'nicename' => 'Redo option', 
		'description' => 'Redo previous formatting changes. Not useful once you save. Built in Wordpress button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 29
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'wp_adv_end', 
		'nicename' => 'END - Advanced toolbar', 
		'description' => '<strong>This is a place holder, not a true button.</strong> Used as the END point for FIRST ROW buttons that you want in the <strong>Advanced toolbar</strong>. Note these buttons may appear hidden see Advanced Toolbar Button.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes', 
		'separator' => 'no', 
		'row' => 1, 
		'position' => 30
	));
	
	// End WordPress Defaults
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'anchor', 
		'nicename' => 'Anchors', 
		'description' => 'Create named anchors.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'no', 
		'separator' => 'no', 
		'row' => 0, 
		'position' => 0
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'sub', 
		'nicename' => 'Subscript', 
		'description' => 'Format text as Subscript.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'no', 
		'separator' => 'no', 
		'row' => 0, 
		'position' => 0
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'sup', 
		'nicename' => 'Superscript', 
		'description' => 'Format text as Superscript.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'no', 
		'separator' => 'no', 
		'row' => 0, 
		'position' => 0
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'backcolor', 
		'nicename' => 'Background color', 
		'description' => 'Set background color for selected tag or text. ', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'no', 
		'separator' => 'no', 
		'row' => 0, 
		'position' => 0
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'code', 
		'nicename' => 'HTML Source', 
		'description' => 'View and edit the HTML source code.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'no', 
		'separator' => 'no', 
		'row' => 0, 
		'position' => 0
	));
	
	$wp_super_edit_registry->register_tinymce_button( array(
		'name' => 'wp_page', 
		'nicename' => 'Wordpress Next Page Tag', 
		'description' => 'Insert Wordpress Next Page tag to divide page content into multiple views.', 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'no', 
		'separator' => 'no', 
		'row' => 0, 
		'position' => 0
	));

}

/**
* WP Super Edit Plugin Folder Scan
*
* Scans tinymce_plugin folder for config files with registration commands.
*
*/
function wp_super_edit_plugin_folder_scan() {

	$wp_super_edit_registry = new wp_super_edit_registry();
	$wp_super_edit_registry->get_registered();
	
	$tinymce_plugins = @ dir( $wp_super_edit_registry->tinymce_plugins_path );
	
	while( ( $tinymce_plugin = $tinymce_plugins->read() ) !== false) {
	
		$tinymce_plugin_path = $wp_super_edit_registry->tinymce_plugins_path . $tinymce_plugin . '/';
		
		if ( is_dir( $tinymce_plugin_path ) && is_readable( $tinymce_plugin_path ) ) {
			if ( $tinymce_plugin{0} == '.' || $tinymce_plugin == '..' ) continue;

			$tinymce_plugin_dir = @ dir( $tinymce_plugin_path );
			
			while ( ( $tinymce_plugin_config = $tinymce_plugin_dir->read() ) !== false) {
			
				if ( $tinymce_plugin_config == 'config.php' ) {
					include_once( $tinymce_plugin_path . $tinymce_plugin_config );
					break;
				}
				
			}
		}
	}
	
}

/**
* WP Super Edit Activation
*
* Creates database tables and installs default settings.
*
*/
function wp_super_edit_activate() {
	wp_super_edit_install_db_tables();
	wp_super_edit_wordpress_button_defaults();
	wp_super_edit_plugin_folder_scan();
}

?>