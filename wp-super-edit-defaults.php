<?php

function wp_super_edit_db_tables() {
	global $wpdb, $wp_super_edit;
	

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
	 description text NOT NULL,
	 provider varchar(60) NOT NULL default '',
	 plugin varchar(60) NOT NULL default '',
	 status varchar(20) NOT NULL default 'no',
	 separator varchar(10) NOT NULL default 'no',
	 row tinyint UNSIGNED NOT NULL default 0,
	 position tinyint UNSIGNED NOT NULL default 0,
	 PRIMARY KEY (id,name),
	 UNIQUE KEY name (name)
	) $charset_collate;
	CREATE TABLE $wp_super_edit->db_users (
	 id bigint(20) NOT NULL auto_increment,
	 user_id bigint(20) NOT NULL default 0,
	 user_name varchar(60) NOT NULL default '',
	 button_row_1 text NOT NULL,
	 button_row_2 text NOT NULL,
	 button_row_3 text NOT NULL,
	 button_row_4 text NOT NULL,
	 editor_options text NOT NULL,
	 PRIMARY KEY (id,user_name),
	 UNIQUE KEY id (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	dbDelta($install_sql);
}




function wp_super_edit_install_defaults($user_id) {
	global $wpdb, $wp_super_edit, $wp_super_edit_db;


	// Default category
	$cat_name = $wpdb->escape(__('Uncategorized'));
	$cat_slug = sanitize_title(__('Uncategorized'));
	$wpdb->query("INSERT INTO $wpdb->terms (name, slug, term_group) VALUES ('$cat_name', '$cat_slug', '0')");
	$wpdb->query("INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ('1', 'category', '', '0', '1')");

	// Default link category
	$cat_name = $wpdb->escape(__('Blogroll'));
	$cat_slug = sanitize_title(__('Blogroll'));
	$wpdb->query("INSERT INTO $wpdb->terms (name, slug, term_group) VALUES ('$cat_name', '$cat_slug', '0')");
	$wpdb->query("INSERT INTO $wpdb->term_taxonomy (term_id, taxonomy, description, parent, count) VALUES ('2', 'link_category', '', '0', '7')");

	// Now drop in some default links
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://codex.wordpress.org/', 'Documentation', 0, '', '');");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (1, 2)" );

	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://wordpress.org/development/', 'Development Blog', 0, 'http://wordpress.org/development/feed/', '');");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (2, 2)" );

	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://wordpress.org/extend/ideas/', 'Suggest Ideas', 0, '', '');");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (3, 2)" );

	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://wordpress.org/support/', 'Support Forum', 0, '', '');");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (4, 2)" );

	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://wordpress.org/extend/plugins/', 'Plugins', 0, '', '');");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (5, 2)" );

	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://wordpress.org/extend/themes/', 'Themes', 0, '', '');");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (6, 2)" );

	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://planet.wordpress.org/', 'WordPress Planet', 0, '', '');");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (7, 2)" );

	// First post
	$now = date('Y-m-d H:i:s');
	$now_gmt = gmdate('Y-m-d H:i:s');
	$first_post_guid = get_option('home') . '/?p=1';
	$wpdb->query("INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_excerpt, post_title, post_category, post_name, post_modified, post_modified_gmt, guid, comment_count, to_ping, pinged, post_content_filtered) VALUES ($user_id, '$now', '$now_gmt', '".$wpdb->escape(__('Welcome to WordPress. This is your first post. Edit or delete it, then start blogging!'))."', '', '".$wpdb->escape(__('Hello world!'))."', '0', '".$wpdb->escape(__('hello-world'))."', '$now', '$now_gmt', '$first_post_guid', '1', '', '', '')");
	$wpdb->query( "INSERT INTO $wpdb->term_relationships (`object_id`, `term_taxonomy_id`) VALUES (1, 1)" );

	// Default comment
	$wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_date, comment_date_gmt, comment_content) VALUES ('1', '".$wpdb->escape(__('Mr WordPress'))."', '', 'http://wordpress.org/', '$now', '$now_gmt', '".$wpdb->escape(__('Hi, this is a comment.<br />To delete a comment, just log in and view the post&#039;s comments. There you will have the option to edit or delete them.'))."')");

	// First Page
	$wpdb->query("INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_excerpt, post_title, post_category, post_name, post_modified, post_modified_gmt, post_status, post_type, to_ping, pinged, post_content_filtered) VALUES ($user_id, '$now', '$now_gmt', '".$wpdb->escape(__('This is an example of a WordPress page, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many pages like this one or sub-pages as you like and manage all of your content inside of WordPress.'))."', '', '".$wpdb->escape(__('About'))."', '0', '".$wpdb->escape(__('about'))."', '$now', '$now_gmt', 'publish', 'page', '', '', '')");
}

?>