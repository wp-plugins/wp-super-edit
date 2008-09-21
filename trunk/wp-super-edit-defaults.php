<?php


/**
* WP Super Edit Install Database Tables
*
* Installs default database tables for WP Super Edit.
*/
function wp_super_edit_install_db_tables() {
	global $wpdb, $wp_super_edit;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	if ( !is_object( $wp_super_edit ) ) {
		$wp_super_edit = new wp_super_edit_admin();
	}

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
	 PRIMARY KEY (id,name),
	 UNIQUE KEY id (id)
	) $charset_collate;
	CREATE TABLE $wp_super_edit->db_users (
	 id bigint(20) NOT NULL auto_increment,
	 user_name varchar(60) NOT NULL default '',
	 user_nicename varchar(60) NOT NULL default '',
	 user_type text NOT NULL default '',
	 editor_options text NOT NULL,
	 PRIMARY KEY (id,user_name),
	 UNIQUE KEY id (id)
	) $charset_collate;";
	
	dbDelta($install_sql);
	
	$wp_super_edit->is_db_installed = true;
		
}

/**
* WP Super Edit Default User
*
* Sets default user settings from most recent TinyMCE scan, sets initial options, and removes unnecessary WordPress options
*/
function wp_super_edit_set_user_default() {
	global $wp_super_edit;

	$tiny_mce_scan = get_option( 'wp_super_edit_tinymce_scan' );
	
	$wp_super_edit->register_user_settings( 'wp_super_edit_default', 'Default Editor Settings', $tiny_mce_scan, 'single' );

	$wp_super_edit->set_option( 'tinymce_scan', $tiny_mce_scan );
	$wp_super_edit->set_option( 'management_mode', 'single' );
	
	delete_option( 'wp_super_edit_tinymce_scan' );
	
	/**
	* Remove old options for versions 1.5 
	*/	
	delete_option( 'superedit_options' );
	delete_option( 'superedit_buttons' );
	delete_option( 'superedit_plugins' );
}

/**
* WP Super Edit WordPress Button Defaults
*
* Registers known default TinyMCE buttons included in default WordPress installation
*/
function wp_super_edit_wordpress_button_defaults() {
	global $wp_super_edit;

	if ( !$wp_super_edit->is_db_installed ) return;
		
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'bold', 
		'nicename' => __('Bold'), 
		'description' => __('Bold content with strong HTML tag. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'italic', 
		'nicename' => __('Italic'), 
		'description' => __('Italicize content with em HTML tag. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'strikethrough', 
		'nicename' => __('Strikethrough'), 
		'description' => __('Strike out content with strike HTML tag. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'bullist', 
		'nicename' => __('Bulleted List'), 
		'description' => __('An unordered list. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'numlist', 
		'nicename' => __('Numbered List'), 
		'description' => __('An ordered list. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'blockquote', 
		'nicename' => __('Block Quote'), 
		'description' => __('Blockquotes are used when quoting other content. Usually this content is displayed as indented.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));

	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'justifyleft', 
		'nicename' => __('Left Justification'), 
		'description' => __('Set the alignment to left justification. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'justifycenter', 
		'nicename' => __('Center Justification'), 
		'description' => __('Set the alignment to center justification. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'justifyright', 
		'nicename' => __('Right Justification'), 
		'description' => __('Set the alignment to right justification. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'link', 
		'nicename' => __('Create Link'), 
		'description' => __('Create a link. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'unlink', 
		'nicename' => __('Remove Link'), 
		'description' => __('Remove a link. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'wp_more', 
		'nicename' => __('Wordpress More Tag'), 
		'description' => __('Insert Wordpress MORE tag to divide content to multiple views. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'spellchecker', 
		'nicename' => __('Spell Check'), 
		'description' => __('Wordpress spell check. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));

	$wp_super_edit->register_tinymce_button( array(
		'name' => 'fullscreen', 
		'nicename' => __('Full Screen'), 
		'description' => __('Toggle Full Screen editor mode.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'wp_adv', 
		'nicename' => __('Show/Hide Advanced toolbar'), 
		'description' => __('Built in Wordpress button <strong>normally hidden</strong>. When pressed it will show extra rows of buttons (or press Ctrl-Alt-V on FF, Alt-V on IE).'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'formatselect', 
		'nicename' => __('Paragraphs and Headings'), 
		'description' => __('Set Paragraph or Headings for content.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'underline', 
		'nicename' => __('Underline Text'), 
		'description' => __('Built in Wordpress button to underline selected text.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'justifyfull', 
		'nicename' => __('Full Justification'), 
		'description' => __('Set the alignment to full justification. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'forecolor', 
		'nicename' => __('Foreground color'), 
		'description' => __('Set foreground or text color. May produce evil font tags. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'pastetext', 
		'nicename' => __('Paste as Text'), 
		'description' => __('Paste clipboard text and remove formatting. Useful for pasting text from applications that produce substandard HTML. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'pasteword', 
		'nicename' => __('Paste from Microsoft Word'), 
		'description' => __('Attempts to clean up HTML produced by Microsoft Word during cut and paste. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'removeformat', 
		'nicename' => __('Remove HTML Formatting'), 
		'description' => __('Removes HTML formatting from selected item. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));

	$wp_super_edit->register_tinymce_button( array(
		'name' => 'media', 
		'nicename' => __('Media'), 
		'description' => __('Add or edit embedded media like Flash, Quicktime, or Windows Media. Different from WordPress Media tools.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'charmap', 
		'nicename' => __('Special Characters'), 
		'description' => __('Insert special characters or entities using a visual interface. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));

	$wp_super_edit->register_tinymce_button( array(
		'name' => 'outdent', 
		'nicename' => __('Decrease Indentation'), 
		'description' => __('This will decrease the level of indentation based on content position. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'indent', 
		'nicename' => __('Increase Indentation'), 
		'description' => __('This will increase the level of indentation based on content position. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));

	$wp_super_edit->register_tinymce_button( array(
		'name' => 'undo', 
		'nicename' => __('Undo option'), 
		'description' => __('Undo previous formatting changes. Not useful once you save. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'redo', 
		'nicename' => __('Redo option'), 
		'description' => __('Redo previous formatting changes. Not useful once you save. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));

	$wp_super_edit->register_tinymce_button( array(
		'name' => 'wp_help', 
		'nicename' => __('Wordpress Help'), 
		'description' => __('Built in Wordpress help documentation. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	

	// End WordPress Defaults
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'cleanup', 
		'nicename' => __('Clean up HTML'), 
		'description' => __('Attempts to clean up bad HTML in the editor. Built in Wordpress button.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'image', 
		'nicename' => __('Image Link'), 
		'description' => __('Insert linked image. Wordpress default editor option for first row.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));	
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'anchor', 
		'nicename' => __('Anchors'), 
		'description' => __('Create named anchors.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'sub', 
		'nicename' => __('Subscript'), 
		'description' => __('Format text as Subscript.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'sup', 
		'nicename' => __('Superscript'), 
		'description' => __('Format text as Superscript.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'backcolor', 
		'nicename' => __('Background color'), 
		'description' => __('Set background color for selected tag or text. '), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'code', 
		'nicename' => __('HTML Source'), 
		'description' => __('View and edit the HTML source code.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));
	
	$wp_super_edit->register_tinymce_button( array(
		'name' => 'wp_page', 
		'nicename' => __('Wordpress Next Page Tag'), 
		'description' => __('Insert Wordpress Next Page tag to divide page content into multiple views.'), 
		'provider' => 'wordpress', 
		'plugin' => '', 
		'status' => 'yes'
	));

}

?>