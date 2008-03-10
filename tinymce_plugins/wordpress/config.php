; <?php die( 'This file should not be used directly' ); ?>
; -- do not modify the above line for security reasons --
;
; WP Super Edit Plugin Configuration file
;
; This is a plugin configuration file for WP Super Edit.
; Each TinyMCE plugin added to WP Super Edit should have similar options.
;

; WP Super Edit options for this plugin

desc = "Modified Wordpress TinyMCE"
notice = "<strong>Experimental:</strong> Designed to allow raw and mostly unfiltered HTML editing using the code tab. <b>Beware</b>, this plugin will disable Wordpress autop functions."
status = N
callbacks = "superedit_disable_wpautop, superedit_custom_editor_wpcss"

; Tiny MCE Buttons provided by this plugin

