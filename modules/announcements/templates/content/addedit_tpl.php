<script language="JavaScript" type="text/javascript" >
    jQuery('input[name=recipienttarget]:radio').livequery(function() {
        if (jQuery('input[name=recipienttarget]:radio:checked').val() == 'site') {
            jQuery('.context_option').attr('disabled', 'disabled');
        } else {
            jQuery('.context_option').removeAttr('disabled');
        }
    });

    jQuery('input[name=recipienttarget]:radio').livequery('click', function() {
        if (jQuery('input[name=recipienttarget]:radio:checked').val() == 'site') {
            jQuery('.context_option').attr('disabled', 'disabled');
        } else {
            jQuery('.context_option').removeAttr('disabled');
        }
    });

</script>
<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');

$this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery.livequery.js', 'jquery'));

$outerLayer = "";

$header = new htmlHeading();
$header->type = 1;
if ($mode == 'edit') {
    $header->str = $this->objLanguage->languageText('mod_announcements_update', 'announcements', 'Edit Announcement');
    $formAction = 'update';
} else {
    $header->str = $this->objLanguage->languageText('mod_announcements_addnewannouncement', 'announcements', 'Add New Announcement');
    $formAction = 'save';
}
$outerLayer = $header->show();

$form = new form ('announcement', $this->uri(array('action'=>$formAction)));

if ($mode == 'edit') {
    $hiddenInput = new hiddeninput('id', $announcement['id']);
    $form->addToForm($hiddenInput->show());
}

$table = $this->newObject('htmltable', 'htmlelements');

if ($mode == 'fixup') {
    $table->startRow();
    $table->addCell('<font color="#ff0000">'. $errorMessage.'</font>');
    $table->endRow();
}

$table->startRow();
$label = new label ($this->objLanguage->languageText('word_title', 'system', 'Title'), 'input_title');
$titlefield = new textinput('title');
$titlefield->size = 60;
/*
if(!empty($enteredtitle)){
    $title->value = $enteredtitle;
} elseif
*/
if ($mode == 'edit') {
    $titlefield->value = $announcement['title'];
} else if ($mode == 'fixup') {
    $titlefield->value = $title;
}
$table->addCell($label->show(), 120);
$table->addCell($titlefield->show());
$table->endRow();

if (
    $mode == 'add'
        && count($lecturerContext) > 0
    || $mode == 'edit'
        && $announcement['contextid'] == 'context'
    || $mode == 'fixup'
        && $recipienttarget == 'context') {
    $contextsList = '';
    if (count($lecturerContext) > 0) {
        foreach ($lecturerContext as $context) {
            $checkbox = new checkbox ('contexts[]', $context);
            $checkbox->value = $context;
            $checkbox->cssId = 'context_'.$context;
            $checkbox->cssClass = 'context_option';
            if ($mode == 'add' && $context == $this->objContext->getContextCode()) {
                $checkbox->ischecked = TRUE;
            } else if ($mode == 'edit') {
                if (in_array($context, $contextAnnouncementList)) {
                    $checkbox->ischecked = TRUE;
                }
            } else if ($mode == 'fixup') {
                if (in_array($context, $contexts)) {
                    $checkbox->ischecked = TRUE;
                }
            }
            $contextRow=$this->objContext->getContext($context);
            $label = new label(' '.$contextRow['title'], 'context_'.$context);
            $contextsList .= '<br />&nbsp; &nbsp; '.$checkbox->show().$label->show();
        }
    }
}

if ($mode == 'add') {
    if ($isAdmin && count($lecturerContext) > 0) {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_announcements_sendto', 'announcements', 'Send to').':');
        $objRecipientTarget = new radio ('recipienttarget');
        $objRecipientTarget->setBreakSpace('<br />');
        $objRecipientTarget->addOption('site', $this->objLanguage->languageText('mod_announcements_allusers', 'announcements', 'Site - All Users'));
        $objRecipientTarget->addOption('context', $this->objLanguage->code2Txt('mod_announcements_onlytofollowing', 'announcements', NULL, 'Only to the following [-contexts-]'));
        $objRecipientTarget->setSelected('context');
        $table->addCell($objRecipientTarget->show().$contextsList);
        $table->endRow();
    } else if ($isAdmin) {
        $objRecipientTarget = new hiddeninput('recipienttarget', 'site');
        $form->addToForm($objRecipientTarget->show());
    } else {
        $objRecipientTarget = new hiddeninput('recipienttarget', 'context');
        $form->addToForm($objRecipientTarget->show());
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_announcements_sendto', 'announcements', 'Send to'));
        $table->addCell($this->objLanguage->code2Txt('mod_announcements_followingcontexts', 'announcements', NULL, 'the following [-contexts-]').':'.$contextsList);
        $table->endRow();
    }
} else if ($mode == 'edit') {
    $objRecipientTarget = new hiddeninput('recipienttarget', $announcement['contextid']);
    $form->addToForm($objRecipientTarget->show());
    if ($announcement['contextid'] == 'site') {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('word_type', 'system', 'Type').':');
        $table->addCell($this->objLanguage->languageText('mod_announcements_siteannouncement', 'announcements', 'Site Announcement'));
        $table->endRow();
    } else {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_announcements_sendto', 'announcements', 'Send to').':');
        $table->addCell($this->objLanguage->code2Txt('mod_announcements_followingcontexts', 'announcements', NULL, 'the following [-contexts-]').':'.$contextsList);
        $table->endRow();
    }
} else if ($mode == 'fixup') {
    $objRecipientTarget = new hiddeninput('recipienttarget', $recipienttarget);
    $form->addToForm($objRecipientTarget->show());
    if ($recipienttarget == 'site') {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('word_type', 'system', 'Type').':');
        $table->addCell($this->objLanguage->languageText('mod_announcements_siteannouncement', 'announcements', 'Site Announcement'));
        $table->endRow();
    } else {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_announcements_sendto', 'announcements', 'Send to').':');
        $table->addCell($this->objLanguage->code2Txt('mod_announcements_followingcontexts', 'announcements', NULL, 'the following [-contexts-]').':'.$contextsList);
        $table->endRow();
    }
}

// Email to users
$table->startRow();
$email2Users = new radio ('email');
$email2Users->addOption('N', $this->objLanguage->languageText('word_no', 'system', 'No'));
$email2Users->addOption('Y', $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
$email2Users->setSelected('N');
$email2Users->setBreakSpace(' &nbsp; ');
$table->addCell($this->objLanguage->languageText('mod_announcements_emailtousers', 'announcements', 'Email to Users'));
$table->addCell($email2Users->show());
$table->endRow();

// Message
$table->startRow();
$htmlArea = $this->newObject('htmlarea', 'htmlelements');
$htmlArea->name = 'message';
/*
if(!empty($enteredmessage)){
    $title->value = $enteredmessage;
} elseif
*/
if ($mode == 'edit') {
    $htmlArea->value = $announcement['message'];
} else if ($mode == 'fixup') {
    $htmlArea->value = $message;
}
$table->addCell($this->objLanguage->languageText('word_message', 'system', 'Message'));
$table->addCell($htmlArea->show());
$table->endRow();

/*
$table->startRow();
$table->addCell('&nbsp;');
$table->addCell('&nbsp;');
$table->endRow();
*/

// Post Announcement
$table->startRow();
$button = new button ('send', $this->objLanguage->languageText('mod_announcements_postannouncement', 'announcements', 'Post Announcement'));
$button->setToSubmit();
$table->addCell('&nbsp;');
$table->addCell($button->show());
$table->endRow();

$form->addToForm($table->show());

$modeInput = new hiddeninput('mode', $mode);
$form->addToForm($modeInput->show());

$outerLayer .= $form->show();

echo
    '<div class="outerwrapper">'
    . $outerLayer
    . '</div>';

$backLink = new link ($this->uri(array()));
$backLink->link = $this->objLanguage->languageText('mod_announcements_back', 'announcements', 'Back to Announcements');
/*
echo "<div class='modulehome'></div><div class='modulehomelink'>" . $backLink->show() . '</div>';
*/
echo $backLink->show();
/*
$label = new label ($this->objLanguage->languageText('word_title', 'system', 'Title'), 'input_title');
$title = new textinput('title');
$label = new label ($this->objLanguage->languageText('word_title', 'system', 'Title'), 'input_title');
$title = new textinput('title');
*/
?>