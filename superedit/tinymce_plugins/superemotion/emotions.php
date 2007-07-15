<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php

require_once('../../../../../wp-config.php');

?>
	<title>{$lang_superemotions_title}</title>
	<script language="javascript" type="text/javascript" src="../../../../../wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="jscripts/functions.js"></script>
	<base target="_self" />
</head>
<body style="display: none">
	<div align="center">
		<div class="title">{$lang_superemotions_title}:<br /><br /></div>

		<table border="0" cellspacing="0" cellpadding="4">
		  <tr>
			<td><a href="javascript:insertEmotion('icon_arrow.gif','lang_superemotions_arrow', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_arrow.gif" width="15" height="15" border="0" alt="{$lang_superemotions_arrow}" title="{$lang_superemotions_arrow}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_exclaim.gif','lang_superemotions_exclaim', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_exclaim.gif" width="15" height="15" border="0" alt="{$lang_superemotions_exclaim}" title="{$lang_superemotions_exclaim}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_question.gif','lang_superemotions_question', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_question.gif" width="15" height="15" border="0" alt="{$lang_superemotions_question}" title="{$lang_superemotions_question}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_idea.gif','lang_superemotions_idea', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_idea.gif" width="15" height="15" border="0" alt="{$lang_superemotions_idea}" title="{$lang_superemotions_idea}" /></a></td>
		  </tr>		
		  <tr>
			<td><a href="javascript:insertEmotion('icon_smile.gif','lang_superemotions_smile', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_smile.gif" width="15" height="15" border="0" alt="{$lang_superemotions_smile}" title="{$lang_superemotions_smile}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_biggrin.gif','lang_superemotions_biggrin', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_biggrin.gif" width="15" height="15" border="0" alt="{$lang_superemotions_biggrin}" title="{$lang_superemotions_biggrin}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_lol.gif','lang_superemotions_lol', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_lol.gif" width="15" height="15" border="0" alt="{$lang_superemotions_lol}" title="{$lang_superemotions_lol}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_wink.gif','lang_superemotions_wink', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_wink.gif" width="15" height="15" border="0" alt="{$lang_superemotions_wink}" title="{$lang_superemotions_wink}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_cool.gif','lang_superemotions_cool', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_cool.gif" width="15" height="15" border="0" alt="{$lang_superemotions_cool}" title="{$lang_superemotions_cool}" /></a></td>
		  </tr>
		  <tr>
			<td><a href="javascript:insertEmotion('icon_neutral.gif','lang_superemotions_neutral', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_neutral.gif" width="15" height="15" border="0" alt="{$lang_superemotions_neutral}" title="{$lang_superemotions_neutral}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_confused.gif','lang_superemotions_confused', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_confused.gif" width="15" height="15" border="0" alt="{$lang_superemotions_confused}" title="{$lang_superemotions_confused}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_razz.gif','lang_superemotions_razz', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_razz.gif" width="15" height="15" border="0" alt="{$lang_superemotions_razz}" title="{$lang_superemotions_razz}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_surprised.gif','lang_superemotions_surprised', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_surprised.gif" width="15" height="15" border="0" alt="{$lang_superemotions_surprised}" title="{$lang_superemotions_surprised}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_eek.gif','lang_superemotions_eek', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_eek.gif" width="15" height="15" border="0" alt="{$lang_superemotions_eek}" title="{$lang_superemotions_eek}" /></a></td>
		  </tr>
		  <tr>
			<td><a href="javascript:insertEmotion('icon_redface.gif','lang_superemotions_redface', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_redface.gif" width="15" height="15" border="0" alt="{$lang_superemotions_redface}" title="{$lang_superemotions_redface}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_rolleyes.gif','lang_superemotions_rolleyes', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_rolleyes.gif" width="15" height="15" border="0" alt="{$lang_superemotions_rolleyes}" title="{$lang_superemotions_rolleyes}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_mad.gif','lang_superemotions_mad', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_mad.gif" width="15" height="15" border="0" alt="{$lang_superemotions_mad}" title="{$lang_superemotions_mad}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_sad.gif','lang_superemotions_sad', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_sad.gif" width="15" height="15" border="0" alt="{$lang_superemotions_sad}" title="{$lang_superemotions_sad}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_cry.gif','lang_superemotions_cry', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_cry.gif" width="15" height="15" border="0" alt="{$lang_superemotions_cry}" title="{$lang_superemotions_cry}" /></a></td>
		  </tr>		  
		  <tr>
			<td><a href="javascript:insertEmotion('icon_mrgreen.gif','lang_superemotions_mrgreen', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_mrgreen.gif" width="15" height="15" border="0" alt="{$lang_superemotions_mrgreen}" title="{$lang_superemotions_mrgreen}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_evil.gif','lang_superemotions_evil', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_evil.gif" width="15" height="15" border="0" alt="{$lang_superemotions_evil}" title="{$lang_superemotions_evil}" /></a></td>
			<td><a href="javascript:insertEmotion('icon_twisted.gif','lang_superemotions_twisted', '<?php bloginfo('url'); ?>/wp-includes/images/smilies/');"><img src="<?php bloginfo('url'); ?>/wp-includes/images/smilies/icon_twisted.gif" width="15" height="15" border="0" alt="{$lang_superemotions_twisted}" title="{$lang_superemotions_twisted}" /></a></td>
		  </tr>
		</table>
	</div>
</body>
</html>