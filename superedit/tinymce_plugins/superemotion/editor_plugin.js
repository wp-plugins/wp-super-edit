tinyMCE.importPluginLanguagePack('superemotion');var TinyMCE_EmotionsPlugin={getInfo:function(){return{longname:'Wordpress Emoticons',author:'Jess Planck after Moxiecode Systems',authorurl:'http://www.funroe.net',infourl:'http://www.funroe.net/projects/superedit/',version:tinyMCE.majorVersion+"."+tinyMCE.minorVersion}},getControlHTML:function(cn){switch(cn){case"superemotions":return tinyMCE.getButtonHTML(cn,'lang_superemotions_desc','{$pluginurl}/images/emotions.gif','mceEmotion')}return""},execCommand:function(editor_id,element,command,user_interface,value){switch(command){case"mceEmotion":var template=new Array();template['file']=this.baseURL+'/emotions.php';template['width']=160;template['height']=160;template['width']+=tinyMCE.getLang('lang_emotions_delta_width',0);template['height']+=tinyMCE.getLang('lang_emotions_delta_height',0);tinyMCE.openWindow(template,{editor_id:editor_id,inline:"yes"});return true}return false}};tinyMCE.addPlugin('superemotion',TinyMCE_EmotionsPlugin);