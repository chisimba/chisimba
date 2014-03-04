<?php
//do the check to check if TII is accessable
$objSysConfig = $this->getObject('altconfig', 'config');
$objExtJs = $this->getObject('extjs', 'ext');
$objExtJs->show();
$ext = "";
$ext.= $this->getJavaScriptFile('extjsExtendHTMLEditorToolbar.js', 'liftclub');
$ext.= $this->getJavaScriptFile('Ext.ux.grid.Search.js', 'liftclub');
$ext.= $this->getJavaScriptFile('sendmessagesform.js', 'liftclub');
$ext.= $this->getJavaScriptFile('outboxinterface.js', 'liftclub');
//$ext .=$this->getJavaScriptFile('ext-3.0-rc2/examples/shared/examples.js', 'ext');
//setup the dynamicuri
$this->appendArrayVar('headerParams', '
	        	<script type="text/javascript">	        		
	        		var baseUri = "' . $objSysConfig->getsiteRoot() . 'index.php";
           var lang = new Array();
           lang["liftclubname"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_liftclubname', 'liftclub', NULL, 'Lift Club')) . '";
           lang["time"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_time', 'liftclub', NULL, 'Time')) . '";
           lang["sender"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_sender', 'liftclub', NULL, 'Sender')) . '";
           lang["title"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_title', 'liftclub', NULL, 'Title')) . '";
           lang["messages"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_messages', 'liftclub', NULL, 'Messages')) . '";
           lang["message"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_message', 'liftclub', NULL, 'Message')) . '";
           lang["failure"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_failure', 'liftclub', NULL, 'Failure')) . '";
           lang["msgtrashsuccess"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_msgtrashsuccess', 'liftclub', NULL, 'Message Trashed Successfully')) . '";
           lang["phrasemsgnotfound"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_phrasemsgnotfound', 'liftclub', NULL, 'Message Body not found')) . '";
           lang["wordof"] =   "' . $this->objLanguage->code2Txt('mod_liftclub_wordof', 'liftclub', NULL, 'of') . '";
           lang["restoremsg"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_restoremsg', 'liftclub', NULL, 'Restore to Inbox/Sent')) . '";
           lang["sendmsgback"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_sendmsgback', 'liftclub', NULL, 'Send message to back to Inbox/Sent')) . '";
           lang["inbox"] =   "' . $this->objLanguage->code2Txt('mod_liftclub_receivedmessages', 'liftclub', NULL, 'Inbox') . '";
           lang["replyto"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_replyto', 'liftclub', NULL, 'Reply to sender')) . '";
           lang["trashedmessages"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_trashedmessages', 'liftclub', NULL, 'Trash Messages')) . '";
           lang["page"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordpage', 'liftclub', NULL, 'Page')) . '";
           lang["wordcreated"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordcreated', 'liftclub', NULL, 'Created')) . '";
           lang["reply"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_reply', 'liftclub', NULL, 'Reply')) . '";
           lang["trashit"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_trashit', 'liftclub', NULL, 'Send to trash')) . '";
           lang["message"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_message', 'liftclub', NULL, 'Message')) . '";
           lang["atleasttwochar"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_atleasttwochar', 'liftclub', NULL, 'Type at least 2 characters')) . '";
           lang["wordsubmit"] =   "' . ucWords($this->objLanguage->code2Txt('word_submit', 'system', NULL, 'Submit')) . '";
           lang["wordsave"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordsave', 'liftclub', NULL, 'Save')) . '";
           lang["wordprocessing"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordprocessing', 'liftclub', NULL, 'Processing')) . '";
           lang["mod_liftclub_senderror"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_senderror', 'liftclub', NULL, 'Error Encountered, try again!')) . '";
           lang["wordreset"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordreset', 'liftclub', NULL, 'Reset')) . '";
           lang["mod_liftclub_pleasewait"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_pleasewait', 'liftclub', NULL, 'Please wait')) . '";
           lang["mod_liftclub_wordcomplete"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordcomplete', 'liftclub', NULL, 'Complete')) . '";
           lang["mod_liftclub_sentsuccessfully"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_sentsuccessfully', 'liftclub', NULL, 'Message Sent Successfully')) . '";
           lang["mod_liftclub_restoredsuccessfully"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_restoredsuccessfully', 'liftclub', NULL, 'Message Restored Successfully')) . '";
           lang["wordcancel"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_wordcancel', 'liftclub', NULL, 'Cancel')) . '";
           lang["sendmsgform"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_sendmsgform', 'liftclub', NULL, 'Send Message Form')) . '";
           lang["displayingrecords"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_displayingrecords', 'liftclub', NULL, 'Displaying Records')) . '";
           lang["norecordstodisplay"] =   "' . ucWords($this->objLanguage->code2Txt('mod_liftclub_norecordstodisplay', 'liftclub', NULL, 'No Records To Display')) . '";
	        	</script>');
$exteditor_js = '<script language="JavaScript" src="' . $this->getResourceUri('ext-3.0-rc2/source/wigets/form/HtmlEditor.js', 'ext') . '" type="text/javascript"></script>';
$this->appendArrayVar('headerParams', $exteditor_js);
//$ext .= '<link rel="stylesheet" href="'.$this->getResourceUri('ColumnNodeUI.css', 'liftclub').'" type="text/css" />';
$ext.= '<link rel="stylesheet" href="skins/_common/css/extjs/silk/silk.css" type="text/css" />';
$ext.= '<link rel="stylesheet" href="skins/_common/css/extjs/menus.css" type="text/css" />';
$ext.= '<link rel="stylesheet" href="skins/_common/css/extjs/buttons.css" type="text/css" />';
$ext.= "<style>
	
			#main-interface{
				padding:10px;
				margin:10px;
			}
			pre {
   				font-size:11px; 
			}
			
			.x-tab-panel-body .x-panel-body {
			    padding:10px;
			}
			
			/* default loading indicator for ajax calls */
			.loading-indicator {
				font-size:8pt;
				background-image:url('../../resources/images/default/grid/loading.gif');
				background-repeat: no-repeat;
				background-position: left;
				padding-left:20px;
			}
			
			.new-tab {
			    background-image:url(../feed-viewer/images/new_tab.gif) !important;
			}
			
			
			.tabs {
			    background-image:url( ../desktop/images/tabs.gif ) !important;
			}
			
			p { width:650px; }

			
			</style>";
$this->appendArrayVar('headerParams', $ext);
?>

<center><div id="mainPanel"></div></center>
<center><div id="find-grid"></div></center>
<div id="combo"></div>
