<?php

//Read mail tamplate
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$washer = $this->getObject('washout', 'utilities');
$objTable = new htmltable();
$objFields = new fieldset();
$objFields->setLegend('<b>' . $message['subject'] . '</b>');

$fromLabel = $this->objLanguage->languageText('mod_uwcelearningmobile_wordfrom', 'uwcelearningmobile');
$toLabel = $this->objLanguage->languageText('mod_uwcelearningmobile_wordto', 'uwcelearningmobile');
$dateLabel = $this->objLanguage->languageText('mod_uwcelearningmobile_wordsentdate', 'uwcelearningmobile');
$subjectLabel = $this->objLanguage->languageText('mod_uwcelearningmobile_wordsubject', 'uwcelearningmobile');
$messageLabel = $this->objLanguage->languageText('mod_uwcelearningmobile_wordmessage', 'uwcelearningmobile');

$from = $this->dbRouting->getName($message['sender_id']);
$to = $this->dbRouting->getName($routing['recipient_id']);
$date = $this->objDate->formatDate($message['date_sent']);
$subject = $message['subject'];
$messagedata = nl2br($washer->parseText($message['message']));
//reply link
$array = array(
    'date' => $this->objDate->formatDate($message['date_sent']),
    'writer' => $from
);
$replyMessageLabel = $this->objLanguage->code2Txt('mod_internalmail_replymessage', 'internalmail', $array);
$replyMessage = $replyMessageLabel . "\n";
$replyMessage.= $message['message'] . "\n";
$replyMessage.= '------------------' . "\n";
//////////////////////////
$replylink = new link($this->uri(array(
                    'action' => 'compose',
                    'userId' => $message['sender_id'],
                    'subject' => 'RE: ' . $message['subject'],
                    'message' => $replyMessage,
                    'emailId' => $message['id']
                )));
$replylink->link = $this->objLanguage->languageText('mod_internalmail_reply', 'internalmail');

//reply all link

$arrUserId = explode('|', $message['recipient_list']);
if (!in_array($message['sender_id'], $arrUserId)) {
    $arrUserId[] = $message['sender_id'];
}
$strUserId = implode('|', $arrUserId);
$replyalllink = new link($this->uri(array(
                    'action' => 'compose',
                    'userId' => $strUserId,
                    'subject' => 'RE: ' . $message['subject'],
                    'message' => $replyMessage,
                    'emailId' => $message['id']
                )));
$replyalllink->link = $this->objLanguage->languageText('mod_internalmail_replyall', 'internalmail');
//////////////////////////
$objTable->startRow();
$objTable->addCell('<b>' . $fromLabel . ':</b>', '', '', '', '', '');
$objTable->addCell($from, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>' . $toLabel . ':</b>', '', '', '', '', '');
$objTable->addCell($to, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>' . $dateLabel . ':</b>', '', '', '', '', '');
$objTable->addCell($date, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>' . $subjectLabel . ':</b>', '', '', '', '', '');
$objTable->addCell($subject, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>' . $messageLabel . ':</b><br /><br />' . $messagedata, '', '', '', '', 'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>' . $replylink->show() . '<b>', '', '', '', '', '');
$objTable->addCell('<b>' . $replyalllink->show() . '<b>', '', '', '', '', '');
$objTable->endRow();

$objFields->addContent($objTable->show());

echo $objFields->show();
$backLink = new link($this->URI(array('action' => 'internalmail')));
$backLink->link = 'Back to Internalmail';
echo $this->homeAndBackLink . ' - ' . $backLink->show() . '</br>';
?>