<?php
/*
Plugin Name: WP Super Edit Upgrade Utility
Plugin URI: http://funroe.net/projects/super-edit/
Description: Utility for upgrading WP Super Edit to latest version
Author: Jess Planck
Version: 2.2.1
Author URI: http://funroe.net

Copyright (c) Jess Planck (http://funroe.net)
WP Super Edit is released under the GNU General Public
License: http://www.gnu.org/licenses/gpl.txt

This is a WordPress plugin (http://wordpress.org). WordPress is
free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free
Software Foundation; either version 2 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

For a copy of the GNU General Public License, write to:

Free Software Foundation, Inc.
59 Temple Place, Suite 330
Boston, MA  02111-1307
USA

You can also view a copy of the HTML version of the GNU General
Public License at http://www.gnu.org/copyleft/gpl.html
*/

// register_activation_hook(__FILE__,'wp_super_edit_upgrader');

function wp_super_edit_upgrader() {
	echo 'Thanks for all the fish!';
}
add_action('init', 'wp_super_edit_upgrader', 5);


function wp_super_edit_upgrader_shutdown() {	
	// Deactivate once completed
	echo 'WP Super Edit Upgrade Completed!';
	deactivate_plugins( __FILE__ );
}
add_action('admin_notices', 'wp_super_edit_upgrader_shutdown', 5);


?>