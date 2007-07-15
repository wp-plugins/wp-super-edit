/**
 * $RCSfile: editor_plugin_src.js,v $
 * $Revision: 1.00 $
 * $Date: 2007/06/22 16:29:38 $
 *
 * @author Jess Planck after Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('superemotion');

// Plucin static class
var TinyMCE_EmotionsPlugin = {
	getInfo : function() {
		return {
			longname : 'Wordpress Emoticons',
			author : 'Jess Planck after Moxiecode Systems',
			authorurl : 'http://www.funroe.net',
			infourl : 'http://www.funroe.net/projects/superedit/',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},
	
	/**
	 * Returns the HTML contents of the emotions control.
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "superemotions":
				return tinyMCE.getButtonHTML(cn, 'lang_superemotions_desc', '{$pluginurl}/images/emotions.gif', 'mceEmotion');
		}

		return "";
	},

	/**
	 * Executes the mceEmotion command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceEmotion":
				var template = new Array();

				template['file'] = this.baseURL + '/emotions.php'; // Relative to theme
				template['width'] = 160;
				template['height'] = 160;

				// Language specific width and height addons
				template['width'] += tinyMCE.getLang('lang_emotions_delta_width', 0);
				template['height'] += tinyMCE.getLang('lang_emotions_delta_height', 0);

				tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});

				return true;
		}

		// Pass to next handler in chain
		return false;
	}
};

// Register plugin
tinyMCE.addPlugin('superemotion', TinyMCE_EmotionsPlugin);
