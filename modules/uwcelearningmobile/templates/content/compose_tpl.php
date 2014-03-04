<?php
//Read mail tamplate
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$washer = $this->getObject('washout', 'utilities');
$this->loadClass('htmlheading', 'htmlelements');

$objHead = new htmlheading();
$objHead->str = '&nbsp;' . $this->objLanguage->languageText('mod_internalmail_compose', 'internalmail');
$objHead->type = 4;
echo $objHead->show();

$objTable = new htmltable();
$objFields = new fieldset();
$objFields->setLegend('');

$composeform = new form('uwcelearningmobile', $this->uri(array(
                    'action' => 'sendmail'
                )));

$reFields = new fieldset();
$reFields->setLegend('<b>' . $this->objLanguage->languageText('word_to', 'system') . ':</b>');
if (is_array($recipientList) && !empty($recipientList)) {
    foreach ($recipientList as $list) {
        $userId = $this->objUser->getUserId($list);
        $name = $this->dbRouting->getName($userId);
        $rmlink = new link($this->URI(array('action' => 'rmrecipient', 'username' => $list)));
        $rmlink->link = 'Remove';
        $toList.= '<p><span id="' . $userId . '">' . $name . ' - ' . $rmlink->show();
        $toList.='&nbsp;</span></p>';
    }
} else {
    $toList = '<i>No Recipients</i>';
}
$reFields->addContent($toList);

//Set up add reciepient link
$addLink = new link($this->URI(array('action' => 'calladdrecipient')));
$addLink->link = 'Add Recipient';
$reFields->addContent('<p>' . $addLink->show() . '</p>');

$objFields->addContent($reFields->show());
$txtsubject = new textinput('subject');
if (isset($subject)) {
    $txtsubject->value = $subject;
}

$txtmessage = new textarea('message', $message, '', '');
$txtmessage->setRows(8);
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_subject', 'system') . ':', '', '', '', '', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($txtsubject->show(), '', '', '', '', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_message', 'system') . ':', '', '', '', '', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($txtmessage->show(), '', '', '', '', '');
$objTable->endRow();

//--- Create a submit button
$objButton = '<input type="submit" value="' . $this->objLanguage->languageText("word_send") . '" />';
$objTable->startRow();
$objTable->addCell('<p>' . $objButton . '</p>', '', '', '', '', '');
$objTable->endRow();

$objFields->addContent($objTable->show());
$composeform->addToForm($objFields->show());
$cform = $composeform->show();
echo $cform;

$backLink = new link($this->URI(array('action' => 'internalmail')));
$backLink->link = $this->objLanguage->languageText('mod_uwcelearningmobile_wordbacktomail', 'uwcelearningmobile');
echo $this->homeAndBackLink . ' - ' . $backLink->show();
?>
