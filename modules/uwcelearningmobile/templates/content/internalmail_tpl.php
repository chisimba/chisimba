<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$objHeader = $this->loadClass('htmlheading', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objFields = $this->loadClass('fieldset', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');

//Compose Message link
$objLink = new link($this->uri(array(
                    'action' => 'compose'
                )));
$objIcon->setIcon('notes');
$objIcon->alt = 'Compose';
$objLink->link = $objIcon->show();


$this->loadClass('htmlheading', 'htmlelements');
$objHead = new htmlheading();
$objHead->str = '&nbsp;' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordinternalmail', 'uwcelearningmobile') . ' - ' . $objLink->show();
$objHead->type = 4;

echo $objHead->show();

$objFields = new fieldset();
$objFields->setLegend('');

// set up folders table
$objTable = new htmltable();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordfolder', 'uwcelearningmobile') . '</b>', '50%', '', '', 'wrapperLightBkg', '');
$objTable->addCell('<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordunread', 'uwcelearningmobile') . '</b>', '25%', '', 'center', 'wrapperLightBkg', '');
$objTable->addCell('<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordtotal', 'uwcelearningmobile') . '</b>', '25%', '', 'center', 'wrapperLightBkg', '');
$objTable->endRow();
$i = 0;
foreach ($arrFolderList as $folder) {
//set up folder link
    $objLink = new link($this->uri(array(
                        'action' => 'internalmail',
                        'folderId' => $folder['id']
                    )));
    $objLink->link = $folder['folder_name'];
    $nameLink = $objLink->show();
    $unreadMail = $folder['unreadmail'];
    $objTable->startRow();
    $objTable->addCell($nameLink, '', '', '', $class, '');
    $objTable->addCell($unreadMail, '', '', 'center', $class, '');
    $objTable->addCell($folder['allmail'], '', '', 'center', $class, '');
    $objTable->endRow();
}
$objFields->addContent($objTable->show());
echo $objFields->show();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$objFields = new fieldset();
$objFields->setLegend('<b>' . $arrFolderData['folder_name'] . '</b>');

// set up folders table
$objTable = new htmltable();
$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordfrom', 'uwcelearningmobile') . '</b>', '25%', '', '', 'wrapperLightBkg', '');
$objTable->addCell('<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_worddate', 'uwcelearningmobile') . '</b>', '25%', '', 'center', 'wrapperLightBkg', '');
$objTable->endRow();
$i = 0;

if (empty($arrEmailListData)) {
    $norec = $this->objLanguage->languageText('mod_uwcelearningmobile_wordnomessage', 'uwcelearningmobile');
    $objTable->startRow();
    $objTable->addCell($norec, NULL, NULL, 'center', 'noRecordsMessage', 'colspan="7"');
    $objTable->endRow();
} else {
    foreach ($arrEmailListData as $message) {

        //set up message link
        $objLink = new link($this->uri(array(
                            'action' => 'readmail',
                            'routingid' => $message['routing_id']
                        )));
        $class = ($i++ % 2 == 0) ? 'odd' : 'even';
        if (!$message['read_email']) {
            $from = '<b>' . $message['fullName'] . '</b>';
            $date = "<b>" . $this->objDate->formatDate($message['date_sent']) . '</b>';
            $objLink->link = '<b>' . $message['subject'] . '</b>';
        } else {
            $from = $message['fullName'];
            $date = $this->objDate->formatDate($message['date_sent']);
            $objLink->link = $message['subject'];
        }
        $subject = $objLink->show();
        $objTable->startRow();
        $objTable->addCell($from, '', '', '', $class, '');
        $objTable->addCell($date, '', '', 'center', $class, '');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell($subject, '', '', '', $class, '');
        $objTable->endRow();
    }
}
$objFields->addContent($objTable->show());
echo $objFields->show();

echo $this->homeAndBackLink;
?>
