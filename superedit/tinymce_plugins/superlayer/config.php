; <?php die( 'This file should not be used directly' ); ?>
; -- do not modify the above line for security reasons --
;
; WP Super Edit Plugin Configuration file
;
; This is a plugin configuration file for WP Super Edit.
; Each TinyMCE plugin added to WP Super Edit should have similar options.
;

; WP Super Edit options for this plugin

desc = "Layers (DIV) Plugin"
notice = "Insert layers using DIV HTML tag. This plugin will change the editor to allow all DIV tags. Provides the Insert Layer, Move Layer Forward, Move Layer Backward, and Toggle Layer Positioning Buttons."
status = N
callbacks = "superedit_allow_div"


; Tiny MCE Buttons provided by this plugin

[insertlayer]
desc = "Insert Layer"
notice = "Insert a layer using the DIV HTML tag. Be careful layers are tricky to position."
status = N
row = 0
position = 0
separator = N
plugin = "superlayer"

[moveforward]
desc = "Move Layer Forward"
notice = "Move selected layer forward in stacked view."
status = N
row = 0
position = 0
separator = N
plugin = "superlayer"

[movebackward]
desc = "Move Layer Backward"
notice = "Move selected layer backward in stacked view."
status = N
row = 0
position = 0
separator = N
plugin = "superlayer"

[absolute]
desc = "Toggle Layer Positioning"
notice = "Toggle the layer positioning as absolute or relative. Be careful layers are tricky to position."
status = N
row = 0
position = 0
separator = N
plugin = "superlayer"

		