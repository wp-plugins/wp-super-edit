; <?php die( 'This file should not be used directly' ); ?>
; -- do not modify the above line for security reasons --
;
; WP Super Edit Plugin Configuration file
;
; This is a plugin configuration file for WP Super Edit.
; Each TinyMCE plugin added to WP Super Edit should have similar options.
;

; WP Super Edit options for this plugin

desc = "Custom CSS Classes"
notice = "Adds Custom styles button and CLASSES from an editor.css file in your <strong>Currently active THEME</strong> directory. Provides the Custom CSS Classes Button."
status = N
callbacks = "superedit_custom_editor_css"

; Tiny MCE Buttons provided by this plugin

[styleselect]
desc = "Custom CSS Classes"
notice = "Shows a drop down list of CSS Classes that the editor has access to."
status = N
row = 0
position = 0
separator = N
plugin = "superclass"
