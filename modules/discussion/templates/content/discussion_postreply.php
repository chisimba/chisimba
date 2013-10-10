<?php
//Sending display to 1 column layout
//ob_start();
//
////$this->setVar('pageSuppressXML',true);
//
//$objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
//echo $objHighlightLabels->show();
//
//// Check if Title has Re: attached to it   
//if (substr($post['post_title'], 0, 3) == 'Re:') {
//    // If it does, simply strip slashes
//    $defaultTitle = stripslashes($post['post_title']);
//    $originalTitle = stripslashes($post['post_title']);
//} else {
//    // Else strip slashes AND append Re: to the title
//    $defaultTitle = 'Re: '.stripslashes($post['post_title']);
//    $originalTitle = 'Re: '.stripslashes($post['post_title']);
//}
//    
//// If result of server-side validation, change default title to posted one
//if ($mode == 'fix') {
//    // Select Posted Title
//    $defaultTitle = $details['title'];
//} 
//
//
//?>
<script type="text/javascript">
//<![CDATA[
function clearForTangent()
{
    postTitle = "//<?php echo (addslashes($defaultTitle)); ?>";

    
    if (document.forms["postReplyForm"].replytype[1].checked)
    {
        
        if (document.forms["postReplyForm"].title.value == "//<?php echo addslashes($originalTitle); ?>".split("'").join("\'"))
        {
            alert ('//<?php echo $this->objLanguage->languageText('mod_discussion_tangentsowntitles', 'discussion'); ?> "<?php echo addslashes($originalTitle); ?>"\n<?php echo $this->objLanguage->languageText('mod_discussion_changetitle', 'discussion'); ?>.');
            document.forms["postReplyForm"].title.value = '';
            document.forms["postReplyForm"].title.focus();
            
            
        }
    }
    
    if (document.forms["postReplyForm"].replytype[0].checked)
    {
        if (document.forms["postReplyForm"].title.value == '')
        {
            document.forms["postReplyForm"].title.value = postTitle.split("'").join("\'");
            "<?php echo (stripslashes($originalTitle)); ?>";
            document.forms["postReplyForm"].title.focus();
        }
    }

}

 function SubmitForm()
    {
    document.forms["postReplyForm"].submit();
    }


//]]>
</script>
//<?php
//
//$this->loadClass('form', 'htmlelements');
//$this->loadClass('textinput', 'htmlelements');
//$this->loadClass('textarea', 'htmlelements');
//$this->loadClass('button', 'htmlelements');
//$this->loadClass('dropdown', 'htmlelements');
//$this->loadClass('label', 'htmlelements');
//$this->loadClass('radio', 'htmlelements');
//$this->loadClass('htmlheading', 'htmlelements');
//$this->loadClass('iframe', 'htmlelements');
//
//
//$header = new htmlheading();
//$header->type=3;
//$header->str=$this->objLanguage->languageText('mod_discussion_postreply', 'discussion').': '.stripslashes($post['post_title']);
//echo $header->show();
//
//if ($mode == 'fix') {
//    echo '<span class="noRecordsMessage error"><strong>'.$this->objLanguage->languageText('mod_discussion_messageisblank', 'discussion').'</strong><br />&nbsp;</span>';
//}
//
//echo $postDisplay;
//
//echo '<br/>';
//$postReplyForm = new form('postReplyForm', $this->uri( array('action'=>'savepostreply', 'type'=>$discussiontype)));
//$postReplyForm->displayType = 3;
//$postReplyForm->addRule('title', $this->objLanguage->languageText('mod_discussion_addtitle', 'discussion'), 'required');
//
//
//$addTable = $this->getObject('htmltable', 'htmlelements');
//$addTable->width='99%';
//$addTable->align='center';
//$addTable->cellpadding = 10;
//
//
//$addTable->startRow();
//$subjectLabel = new label($this->objLanguage->languageText('word_subject', 'system').':', 'input_title');
//$addTable->addCell($subjectLabel->show(), 100);
//
//$titleInput = new textinput('title');
//$titleInput->size = 50;
//
//$titleInput->value = htmlspecialchars($defaultTitle);
//
//$addTable->addCell($titleInput->show());
//
//$addTable->endRow();
//
//// type of post
//$addTable->startRow();
//
//$addTable->addCell('<nobr>'.$this->objLanguage->languageText('mod_discussion_typeofreply', 'discussion').':</nobr>', 100);
//
//$objElement = new radio('replytype');
//$objElement->addOption('reply',$this->objLanguage->languageText('mod_discussion_postasreply', 'discussion'));
//$objElement->addOption('tangent', $this->objLanguage->languageText('mod_discussion_postastangent', 'discussion'));
////$objElement->addOption('moderate','Post Reply as Moderator');
//
//if ($mode == 'fix') {
//    $objElement->setSelected($details['replytype']);
//} else {
//    $objElement->setSelected('reply');
//}
//$objElement->setBreakSpace('<br />');
//
//$objElement->extra = ' onclick="clearForTangent()"';
//    
//
//$addTable->addCell($objElement->show());
//
//$addTable->endRow();
//
//$addTable->startRow();
//
//    $languageLabel = new label($this->objLanguage->languageText('word_language', 'system').':', 'input_language');
//    $addTable->addCell($languageLabel->show(), 100);
//    
//    $languageList = new dropdown('language');
//    $languageCodes = & $this->newObject('languagecode','language');
//    
//    // Sort Associative Array by Language, not ISO Code
//    asort($languageCodes->iso_639_2_tags->codes); 
//    
//    foreach ($languageCodes->iso_639_2_tags->codes as $key => $value) {
//        $languageList->addOption($key, $value);
//    }
//    
//    if ($mode == 'fix') {
//        $languageList->setSelected($details['language']);
//    } else {
//        $languageList->setSelected($languageCodes->getISO($this->objLanguage->currentLanguage()));
//    }
//    $addTable->addCell($languageList->show());
//
//$addTable->endRow();
//
//$addTable->startRow();
//
//$addTable->addCell($this->objLanguage->languageText('word_message').':', 140);
//
//$editor=&$this->newObject('htmlarea','htmlelements');
//$editor->setName('message');
//$editor->setContent('');
//$editor->setRows(20);
//$editor->setColumns('100');
//
//$objContextCondition = &$this->getObject('contextcondition','contextpermissions');
//$this->isContextLecturer = $objContextCondition->isContextMember('Lecturers');
//
//if ($this->contextCode == 'root') {
//    $editor->context = FALSE;
//} else if ($this->isContextLecturer || $objContextCondition->isAdmin()) {
//    $editor->context = TRUE;
//} else {
//    $editor->context = FALSE;
//}
//		
//$addTable->addCell($editor->show());
//
//$addTable->endRow();
//
//// ------------------
//
//if ($discussion['attachments'] == 'Y') {
//    $addTable->startRow();
//    
//  /*  $attachmentsLabel = new label($this->objLanguage->languageText('mod_discussion_attachments', 'discussion').':', 'attachments');
//    $addTable->addCell($attachmentsLabel->show(), 100);
//    
//    $attachmentIframe = new iframe();
//    $attachmentIframe->width='100%';
//    $attachmentIframe->height='100';
//    $attachmentIframe->frameborder='0';
//    $attachmentIframe->src= $this->uri(array('module' => 'discussion', 'action' => 'attachments', 'id'=>$temporaryId, 'discussion' => $discussion['id'], 'type'=>$discussiontype)); 
//    
//    $addTable->addCell($attachmentIframe->show());
//   
//   */
//
//    $attachmentsLabel = new label($this->objLanguage->languageText('mod_discussion_attachments', 'discussion').':', 'attachments');
//    $addTable->addCell($attachmentsLabel->show(), 120);
//
//    $form = new form('saveattachment', $this->uri(array('action'=>'saveattachment')));
//
//    $objSelectFile = $this->newObject('selectfile', 'filemanager');
//    $objSelectFile->name = 'attachment';
//    $form->addToForm($objSelectFile->show());
//
//
//    $hiddenTypeInput = new textinput('discussionType');
//    $hiddenTypeInput->fldType = 'hidden';
//    $hiddenTypeInput->value = $post['type_id'];
//    $form->addToForm($hiddenTypeInput->show());
//
//
//    $hiddenTangentInput = new textinput('parent');
//    $hiddenTangentInput->fldType = 'hidden';
//    $hiddenTangentInput->value = $post['post_id'];
//    $form->addToForm($hiddenTangentInput->show());
//
//    $topicHiddenInput = new textinput('topic');
//    $topicHiddenInput->fldType = 'hidden';
//    $topicHiddenInput->value = $post['topic_id'];
//    $form->addToForm($topicHiddenInput->show());
//
//    $hiddenDiscussionInput = new textinput('discussion');
//    $hiddenDiscussionInput->fldType = 'hidden';
//    $hiddenDiscussionInput->value = $discussion['id'];
//    $form->addToForm($hiddenDiscussionInput->show());
//
//    $hiddenTemporaryId = new textinput('temporaryId');
//    $hiddenTemporaryId->fldType = 'hidden';
//    $hiddenTemporaryId->value = $temporaryId;
//    $form->addToForm($hiddenTemporaryId->show());
//
//    $addTable->addCell($form->show());
//
//    $addTable->endRow();
//}
//
//// ------------------------------
//
//// Show Discussion Subscriptions if enabled
//
//if ($discussion['subscriptions'] == 'Y') {
//	$addTable->startRow();
//	$addTable->addCell($this->objLanguage->languageText('mod_discussion_emailnotification', 'discussion', 'Email Notification').':');
//	$subscriptionsRadio = new radio ('subscriptions');
//	$subscriptionsRadio->addOption('nosubscriptions', $this->objLanguage->languageText('mod_discussion_donotsubscribetothread', 'discussion', 'Do not subscribe to this thread'));
//	$subscriptionsRadio->addOption('topicsubscribe', $this->objLanguage->languageText('mod_discussion_notifytopic', 'discussion', 'Notify me via email when someone replies to this thread'));
//	$subscriptionsRadio->addOption('discussionsubscribe', $this->objLanguage->languageText('mod_discussion_notifydiscussion', 'discussion', 'Notify me of ALL new topics and replies in this discussion.'));
//	$subscriptionsRadio->setBreakSpace('<br />');
//	
//	if ($discussionSubscription) {
//		$subscriptionsRadio->setSelected('discussionsubscribe');
//		$subscribeMessage = $this->objLanguage->languageText('mod_discussion_youaresubscribedtodiscussion', 'discussion', 'You are currently subscribed to the discussion, receiving notification of all new posts and replies.');
//	} else if ($topicSubscription) { 
//        $subscriptionsRadio->setSelected('topicsubscribe');
//		$subscribeMessage = $this->objLanguage->languageText('mod_discussion_youaresubscribedtotopic', 'discussion', 'You are already subscribed to this topic.');
//    } else {
//		$subscriptionsRadio->setSelected('nosubscriptions');
//        $subscribeMessage = $this->objLanguage->languageText('mod_discussion_youaresubscribedtonumbertopic', 'discussion', 'You are currently subscribed to [NUM] topics.');
//        $subscribeMessage = str_replace('[NUM]', $numTopicSubscriptions, $subscribeMessage);
//	}
//	
//	$div = '<div class="discussionTangentIndent">'.$subscribeMessage.'</div>';
//	
//	$addTable->addCell($subscriptionsRadio->show().$div);
//	$addTable->endRow();
//}
//
//// ------------------------------
//
//$addTable->startRow();
//
//$addTable->addCell(' ');
//
//$submitButton = new button('submitbutton', $this->objLanguage->languageText('word_submit', 'system','Submit'));
//$submitButton->cssClass = 'save';
//$submitButton->extra = ' onclick="SubmitForm()"';
//$cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel', 'system'));
//$cancelButton->cssClass = 'cancel';
//$returnUrl = $this->uri(array('action'=>'thread', 'id'=>$post['topic_id'], 'type'=>$discussiontype));
//$cancelButton->setOnClick("window.location='$returnUrl'");
//
//$addTable->addCell($submitButton->show().' / '.$cancelButton->show());
//
//$addTable->endRow();
//
//$postReplyForm->addToForm($addTable);
//
//
//
//echo $postReplyForm->show();
//
//$display = ob_get_contents();
//ob_end_clean();
//
//$this->setVar('middleColumn', $display);
?>
<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixTwo();
?>

<div id="twocolumn">
        <div id="Canvas_Content_Body_Region2">
                {
                "display" : "block",
                "module" : "discussion",
                "block" : "postreply"
                }
        </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>