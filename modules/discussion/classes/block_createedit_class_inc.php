<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_createedit_class_inc
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_createedit extends object {

    var $objLanguage;
    var $contextTitle;
    var $contextCode;
    var $contextObject;
    var $objDiscussion;

    //put your code here
    public function init() {
        $this->setVar('pageSuppressXML', true);
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->title = "Create edit";
        $this->objDiscussion = $this->getObject('dbdiscussion', 'discussion');
        // Get Context Code Settings
        $this->contextObject = & $this->getObject('dbcontext', 'context');
        $this->objLanguage = $this->getObject('language', 'language');
        // If not in context, set code to be 'root' called 'Lobby'
        $this->contextTitle = $this->contextObject->getTitle();
        // Get Context Code Settings
        $this->contextObject = & $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();

        // If not in context, set code to be 'root' called 'Lobby'
        $this->contextTitle = $this->contextObject->getTitle();
    }

    function biuldForm() {
        // Get Context Code Settings
        $contextObject = & $this->getObject('dbcontext', 'context');
//        $contextCode = $contextObject->getContextCode();

        // If not in context, set code to be 'root' called 'Lobby'
        $contextTitle = $contextObject->getTitle();
        $id = $this->getParam('id');
        $script = "<script language='JavaScript' type='text/javascript'>
//<![CDATA[
if(!document.getElementById && document.all) {
    document.getElementById = function(id){ return document.all[id]}
}


    function toggleArchiveInput()
    {
        // alert(document.forms['myForm']);
        if (document.forms['myForm'].archivingRadio[1].checked)
            {
                showhide('dateSelect', 'visible');
            } else{
                showhide('dateSelect', 'hidden');
            }

    }

    function showhide (id, visible)
    {
        var style = document.getElementById(id).style
        style.visibility = visible;
    }
//]]>
</script>";
        $html =  $script;
        $this->setVar('pageSuppressXML', true);

        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        $html .=  $objHighlightLabels->show();


        $discussion = $this->objDiscussion->getDiscussion($id);
        // Check if Discussion exists
        if (!$discussion == false) {
            $action = 'editdiscussion';
        }

        $header = & new htmlheading();
        $header->type = 3;

        $action = $this->getParam('action');
        if ($action == 'editdiscussion') {
            $header->str = $this->objLanguage->languageText('mod_discussion_editdiscussionsettings', 'discussion') . ': ' . $discussion['discussion_name'];
            $formAction = 'editdiscussionsave';
        } else {
            $header->str = $this->objLanguage->languageText('mod_discussion_createNewDiscussion', 'discussion', 'Create New Discussion') . ': ' . $contextTitle;
            $formAction = 'savediscussion';
        }
        if ($action == 'creatediscussion') {
            $html .= $header->show();
        }

        $form = new form('myForm', $this->uri(array('module' => 'discussion', 'action' => $formAction,'id'=>$id)));
        $form->displayType = 3;

        $table = $this->getObject('htmltable', 'htmlelements');
        $table->width = '80%';
        $table->cellpadding = 10;


// --------- New Row ---------- //

        $table->startRow();
        $nameLabel = new label($this->objLanguage->languageText('mod_discussion_nameofdiscussion', 'discussion') . ':', 'input_name');
        $table->addCell('<strong>' . $nameLabel->show() . '</strong>', 120);

        $nameInput = new textinput('name');
        $nameInput->size = 57;
        $nameInput->extra = ' maxlength="50"';

        if ($action == 'editdiscussion') {
            $nameInput->value = $discussion['discussion_name'];
        }

        $table->addCell($nameInput->show(), null, null, null, null, ' colspan="3"');

        $table->endRow();

// --------- New Row ---------- //

        $table->startRow();
        $nameLabel = & new label($this->objLanguage->languageText('word_description', 'system') . ':', 'input_description');
        $table->addCell('<strong>' . $nameLabel->show() . '</strong>', 100);

        $nameInput = new textinput('description');
        $nameInput->size = 100;
        $nameInput->extra = 'maxlength="255"';
        if ($action == 'editdiscussion') {
            $nameInput->value = $discussion['discussion_description'];
        }
        $table->addCell($nameInput->show(), null, null, null, null, ' colspan="3"');

        $table->endRow();

// --------- New Row ---------- //

        if ($action == 'editdiscussion') {

            $table->startRow();

            $table->addCell('<strong>' . $this->objLanguage->languageText('mod_discussion_lockdiscussion', 'discussion') . '</strong>');

            $radioGroup = & new radio('lockdiscussion');
            $radioGroup->setBreakSpace(' / ');

            // The option NO comes before YES - as no is this preferred
            $radioGroup->addOption('N', 'No');
            $radioGroup->addOption('Y', $this->objLanguage->languageText('word_yes', 'system'));

            $radioGroup->setSelected($discussion['discussionlocked']);

            $message = ' - ' . $this->objLanguage->languageText('mod_discussion_explainlocking', 'discussion') . '.';

            $table->addCell($radioGroup->show() . $message, null, null, null, null, ' colspan="3"');

            $table->endRow();
        }


// --------- New Row - Visibility & Rating Discussions ---------- //

        $table->startRow();
        $title = '<nobr>' . $this->objLanguage->languageText('mod_discussion_visible', 'discussion') . ':</nobr>';
        $table->addCell('<strong>' . $title . '</strong>', 100);

        if ($action == 'editdiscussion' && $discussion['defaultdiscussion'] == 'Y') {
            $hiddenIdInput = new textinput('visible');
            $hiddenIdInput->fldType = 'hidden';
            $hiddenIdInput->value = 'default';

            $table->addCell($this->objLanguage->languageText('mod_discussion_defaultdiscussion', 'discussion') . $hiddenIdInput->show());
        } else {
            $radioGroup = new radio('visible');
            $radioGroup->setBreakSpace('&nbsp;&nbsp;');
            $radioGroup->addOption('Y', $this->objLanguage->languageText('word_yes'));
            $radioGroup->addOption('N', $this->objLanguage->languageText('word_no'));

            if ($action == 'editdiscussion') {
                $radioGroup->setSelected($discussion['discussion_visible']);
            } else {
                $radioGroup->setSelected('Y');
            }

            $table->addCell($radioGroup->show());
        }


        $title = '<nobr><strong>' . $this->objLanguage->languageText('mod_discussion_usersrateposts', 'discussion') . ':</strong></nobr>';
        $table->addCell($title, 100);

        $radioGroup = new radio('ratings');
        $radioGroup->setBreakSpace('&nbsp;&nbsp;');
        $radioGroup->addOption('Y', $this->objLanguage->languageText('word_yes', 'system'));
        $radioGroup->addOption('N', $this->objLanguage->languageText('word_no', 'system'));
        if ($action == 'editdiscussion') {
            $radioGroup->setSelected($discussion['ratingsenabled']);
        } else {
            $radioGroup->setSelected('Y');
        }

        $table->addCell($radioGroup->show());
        $table->endRow();

// --------- New Row - Students start Topics & upload attachments ---------- //

        $table->startRow();
        $title = '<nobr><strong>' . ucwords($this->objLanguage->code2Txt('mod_discussion_studentsstartTopics', 'discussion')) . ':</strong></nobr>';
        $table->addCell($title, 100);

        $radioGroup = new radio('student');
        $radioGroup->setBreakSpace('&nbsp;&nbsp;');
        $radioGroup->addOption('Y', $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
        $radioGroup->addOption('N', $this->objLanguage->languageText('word_no', 'system', 'No'));
        if ($action == 'editdiscussion') {
            $radioGroup->setSelected($discussion['studentstarttopic']);
        } else {
            $radioGroup->setSelected('Y');
        }

        $table->addCell($radioGroup->show());
        $title = '<nobr><strong>' . $this->objLanguage->languageText('mod_discussion_usersuploadattachments', 'discussion') . ':</strong></nobr>';
        $table->addCell($title, 100);

        $radioGroup = new radio('attachments');
        $radioGroup->setBreakSpace('&nbsp;&nbsp;');
        $radioGroup->addOption('Y', $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
        $radioGroup->addOption('N', $this->objLanguage->languageText('word_no', 'system', 'No'));
        if ($action == 'editdiscussion') {
            $radioGroup->setSelected($discussion['attachments']);
        } else {
            $radioGroup->setSelected('Y');
        }

        $table->addCell($radioGroup->show());
        $table->endRow();

// --------- New Row - Subscriptions ---------- //

        $table->startRow();
        $title = '<nobr><strong>' . $this->objLanguage->languageText('mod_discussion_enableemailsubscription', 'discussion') . ':</strong></nobr>';
        $table->addCell($title, 100);

        $radioGroup = new radio('subscriptions');
        $radioGroup->setBreakSpace('&nbsp;&nbsp;');
        $radioGroup->addOption('Y', $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
        $radioGroup->addOption('N', $this->objLanguage->languageText('word_no', 'system'));
        if ($action == 'editdiscussion') {
            $radioGroup->setSelected($discussion['subscriptions']);
        } else {
            $radioGroup->setSelected('Y');
        }

        $table->addCell($radioGroup->show());

        $table->addCell('&nbsp;');
        $table->addCell('&nbsp;');
        $table->endRow();


// --------- End Row ---------- //
// --------- New Row ---------- //

        if ($action == 'editdiscussion') {
            $table->startRow();

            $table->addCell('<strong><nobr>' . $this->objLanguage->languageText('mod_discussion_archivelabel', 'discussion') . ':</nobr></strong>', 100);

            $radioGroup = new radio('archivingRadio');
            $radioGroup->setBreakSpace(' / ');

            // The option NO comes before YES - as no is this preferred
            $radioGroup->addOption('N', $this->objLanguage->languageText('word_no', 'system', 'No'));
            $radioGroup->addOption('Y', $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
            $radioGroup->extra = 'onclick="toggleArchiveInput()"';

            $selectDateLink = $this->newObject('datepicker', 'htmlelements');
            $selectDateLink->setName('archivedate');

            if ($discussion['archivedate'] == '' || $discussion['archivedate'] == '0000-00-00') {
                $radioGroup->setSelected('N');
                $selectDateLink->setDefaultDate(date('Y-m-d'));
            } else {
                $radioGroup->setSelected('Y');
                $selectDateLink->setDefaultDate($discussion['archivedate']);
            }


            $cell = $radioGroup->show() . ' <span id="dateSelect"> - ' . $selectDateLink->show() . ' <br /><span class="warning">' . $this->objLanguage->languageText('mod_discussion_archivewarning', 'discussion') . '</span></span>';
            $table->addCell($cell, null, null, null, null, ' colspan="3"');

            $table->endRow();
        }

// --------- End Row ---------- //
        $submitButton = new button('submitbtn', $this->objLanguage->languageText('word_save'));
        $submitButton->cssClass = 'save';
        $submitButton->setToSubmit();

        $cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $returnUrl = $this->uri(array('action' => 'administration'));
        $cancelButton->setOnClick("window.location='$returnUrl'");

        $table->addCell($submitButton->show() . '&nbsp;&nbsp;&nbsp;&nbsp;' . $cancelButton->show(), null, null, null, null, ' colspan="4"');

        if ($action == 'editdiscussion') {
            $hiddenIdInput = & new textinput('id');
            $hiddenIdInput->fldType = 'hidden';
            $hiddenIdInput->value = $discussion['id'];
            $form->addToForm($hiddenIdInput->show());
        }

        $form->addToForm($table->show());

        $form->addRule('name', $this->objLanguage->languageText('mod_discussion_discussionnameneeded', 'discussion'), 'required');
        $form->addRule('description', $this->objLanguage->languageText('mod_discussion_discussiondescriptionneeded', 'discussion'), 'required');

        $html .='<div class="creatediscussion">' . $form->show() . '</div>';
        $this->appendArrayVar('bodyOnLoad', 'toggleArchiveInput();');
        return $html;
    }

    function show() {
        return $this->biuldForm();
    }

}

?>
