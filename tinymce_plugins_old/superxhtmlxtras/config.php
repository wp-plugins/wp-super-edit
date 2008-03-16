; <?php die( 'This file should not be used directly' ); ?>
; -- do not modify the above line for security reasons --
;
; WP Super Edit Plugin Configuration file
;
; This is a plugin configuration file for WP Super Edit.
; Each TinyMCE plugin added to WP Super Edit should have similar options.
;

; WP Super Edit options for this plugin

desc = "XHTML Extras Plugin"
notice = "Allows access to interfaces for some XHTML tags like CITE, ABBR, ACRONYM, DEL and INS. Also can give access to advanced XHTML properties such as javascript events. Provides the Citation, Abbreviation, Acronym, Deletion, Insertion, and XHTML Attributes Buttons."
status = N


; Tiny MCE Buttons provided by this plugin

[cite]
desc = "Citation"
notice = "Indicate a citation using the HTML CITE tag."
status = N
row = 0
position = 0
separator = N
plugin = "superxhtmlxtras"

[abbr]
desc = "Abbreviation"
notice = "Indicate an abbreviation using the HTML ABBR tag."
status = N
row = 0
position = 0
separator = N
plugin = "superxhtmlxtras"

[acronym]
desc = "Acronym"
notice = "Indicate an acronym using the HTML ACRONYM tag."
status = N
row = 0
position = 0
separator = N
plugin = "superxhtmlxtras"

[del]
desc = "Deletion"
notice = "Use the HTML DEL tag to indicate recently deleted content."
status = N
row = 0
position = 0
separator = N
plugin = "superxhtmlxtras"

[ins]
desc = "Insertion"
notice = "Use the HTML INS tag to indicate newly inserted content."
status = N
row = 0
position = 0
separator = N
plugin = "superxhtmlxtras"

[attribs]
desc = "XHTML Attributes"
notice = "Modify advanced attributes and javascript events."
status = N
row = 0
position = 0
separator = N
plugin = "superxhtmlxtras"
