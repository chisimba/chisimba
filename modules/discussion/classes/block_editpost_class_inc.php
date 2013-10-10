<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_editpost_class_inc
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_editpost extends object {

    //put your code here
    function init() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('iframe', 'htmlelements');
    }
    
    function buildForm(){
        

        $js = '
<script type="text/javascript">
    //<![CDATA[

    function SubmitForm()
    {
        document.forms["postReplyForm"].submit();
    }

    //]]>
</script>
';

        echo $js;


        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('mod_discussion_editposttitle', 'discussion') . ': ' . stripslashes($post['post_title']);
        echo $header->show();


        echo '<br/>';
        $postReplyForm = new form('postReplyForm', $this->uri(array('module' => 'discussion', 'action' => 'updatepost')));
        $postReplyForm->displayType = 3;
        $postReplyForm->addRule('title', $this->objLanguage->languageText('mod_discussion_addtitle', 'discussion'), 'required');


        $addTable = $this->getObject('htmltable', 'htmlelements');
        $addTable->width = '99%';
        $addTable->align = 'center';
        $addTable->cellpadding = 10;


        $addTable->startRow();
        $subjectLabel = new label($this->objLanguage->languageText('word_subject') . ':', 'input_title');
        $addTable->addCell($subjectLabel->show(), 100);

        $titleInput = new textinput('title');
        $titleInput->size = 50;

        $titleInput->value = str_replace('"', '&quot;', stripslashes($post['post_title']));

        $addTable->addCell($titleInput->show());

        $addTable->endRow();


        $addTable->startRow();

        $languageLabel = new label($this->objLanguage->languageText('word_language') . ':', 'input_language');
        $addTable->addCell($languageLabel->show(), 100);

        $languageList = new dropdown('language');
        $languageCodes = & $this->newObject('languagecode', 'language');

        foreach ($languageCodes->iso_639_2_tags->codes as $key => $value) {
            $languageList->addOption($key, $value);
        }
        $languageList->setSelected($post['language']);
        $addTable->addCell($languageList->show());

        $addTable->endRow();

        $addTable->startRow();

        $addTable->addCell($this->objLanguage->languageText('word_message') . ':', 140);

        $editor = &$this->newObject('htmlarea', 'htmlelements');
        $editor->setName('message');
        $editor->setContent($post['post_text']);
        $editor->setRows(20);
        $editor->setColumns('100');
        $editor->context = TRUE;

        $addTable->addCell($editor->show());

        $addTable->endRow();

// ------------------

        if ($discussion['attachments'] == 'Y') {
            $addTable->startRow();

            /* $attachmentsLabel = new label($this->objLanguage->languageText('mod_discussion_attachments', 'discussion').':', 'attachments');
              $addTable->addCell($attachmentsLabel->show(), 100);

              $attachmentIframe = new iframe();
              $attachmentIframe->width='100%';
              $attachmentIframe->height='100';
              $attachmentIframe->frameborder='0';
              $attachmentIframe->src= $this->uri(array('module' => 'discussion', 'action' => 'attachments', 'id'=>$temporaryId, 'discussion' => $discussion['id']));
             */


            $attachmentsLabel = new label($this->objLanguage->languageText('mod_discussion_attachments', 'discussion') . ':', 'attachments');
            $addTable->addCell($attachmentsLabel->show(), 120);

            $form = new form('saveattachment', $this->uri(array('action' => 'saveattachment')));

            $objSelectFile = $this->newObject('selectfile', 'filemanager');
            $objSelectFile->name = 'attachment';
            $form->addToForm($objSelectFile->show());

            $hiddenTypeInput = new textinput('discussionType');
            $hiddenTypeInput->fldType = 'hidden';
            $hiddenTypeInput->value = $post['type_id'];
            $form->addToForm($hiddenTypeInput->show());

            $topicHiddenInput = new textinput('topic');
            $topicHiddenInput->fldType = 'hidden';
            $topicHiddenInput->value = $post['topic_id'];
            $form->addToForm($topicHiddenInput->show());

            $hiddenDiscussionInput = new textinput('discussion');
            $hiddenDiscussionInput->fldType = 'hidden';
            $hiddenDiscussionInput->value = $discussion['id'];
            $form->addToForm($hiddenDiscussionInput->show());

            $hiddenPostId = new textinput('post_id');
            $hiddenPostId->fldType = 'hidden';
            $hiddenPostId->value = $post['post_id'];
            $form->addToForm($hiddenPostId->show());

            $hiddenTemporaryId = new textinput('temporaryId');
            $hiddenTemporaryId->fldType = 'hidden';
            $hiddenTemporaryId->value = $temporaryId;
            $form->addToForm($hiddenTemporaryId->show());


            $addTable->addCell($form->show());

            $addTable->endRow();
        }

// ------------------------------

        $addTable->startRow();

        $addTable->addCell(' ');

        $submitButton = new button('submitform', $this->objLanguage->languageText('word_submit'));
        $submitButton->cssClass = 'save';
//$submitButton->setToSubmit();
        $submitButton->extra = ' onclick="SubmitForm()"';

        $cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $cancelButton->cssClass = 'cancel';
        $returnUrl = $this->uri(array('action' => 'thread', 'id' => $post['topic_id']));
        $cancelButton->setOnClick("window.location='$returnUrl'");

        $addTable->addCell($submitButton->show() . ' / ' . $cancelButton->show());

        $addTable->endRow();

        $postReplyForm->addToForm($addTable);


        return $postReplyForm->show();
    }

    function show() {
        return $this->buildForm();
    }

}

?>
