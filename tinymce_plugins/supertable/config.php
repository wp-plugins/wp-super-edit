; <?php die( 'This file should not be used directly' ); ?>
; -- do not modify the above line for security reasons --
;
; WP Super Edit Plugin Configuration file
;
; This is a plugin configuration file for WP Super Edit.
; Each TinyMCE plugin added to WP Super Edit should have similar options.
;

; WP Super Edit options for this plugin

desc = "Tables Plugin"
notice = "Allows the creation and manipulation of tables using the TABLE HTML tag. Provides the Tables and Table Controls Buttons."
status = N

; Tiny MCE Buttons provided by this plugin

[supertable]
desc = "Tables"
notice = "Interface to create and change table, row, and cell properties."
status = N
row = 3
position = 3
separator = N
plugin = "supertable"

[supertablecontrols]
desc = "Table controls"
notice = "Interface to manipulate tables and access to cell and row properties."
status = N
row = 3
separator = Y
plugin = "supertable"
