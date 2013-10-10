<?php
//Address book tamplate
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$objTable = new htmltable();
$objFields = new fieldset();

if ($bookId != NULL) {
    $arrBookData = $this->dbBooks->getBook($bookId);
    $subHeading = $arrBookData['book_name'];
} else {
    $arrContextData = $this->dbContext->getContextDetails($contextCode);
    $subHeading = $arrContextData['menutext'];
}

$objFields->setLegend('<strong>'.$subHeading.'</strong>');
if(empty($users)) {
    $objTable->startRow();
    $objTable->addCell($this->objLanguage->languageText('mod_internalmail_noentries', 'internalmail'), '', '', '', 'noRecordsMessage', 'colspan="5"');
    $objTable->endRow();
    $objFields->addContent($objTable->show());
}
else {
    foreach($users as $user) {
        $objChechbox = new checkbox("users[]");
        $objChechbox->value = $user['userid'];
        $objTable->startRow();
        $objTable->addCell($objChechbox->show(), '', '', '', '', '', 3);
        $objTable->addCell('<p>'.$this->objUser->fullname($user['userid']).'</p>', '', '', '', '', '', 3);
        $objTable->endRow();
    }
    $multiform = new form('uwcelearningmobile', $this->uri(array(
        'action' => 'multirecipient'
    )));
    $multiform->addToForm($objTable->show());

    //--- Create a submit button
    $objButton = '<br/><input type="submit" value="'.$this->objLanguage->languageText('phrase_sendmail').'" />';
    $multiform->addToForm($objButton);
    $objFields->addContent($multiform->show());
}
// set up heading
$objHeader = $this->objLanguage->languageText('mod_internalmail_addressbookentries', 'internalmail').'<br/>';
$Heading = '<strong>'.$objHeader.'</strong>';

echo $Heading;

echo $objFields->show();

$backLink = new link($this->URI(array('action' => 'showbooks')));
$backLink->link = $this->objLanguage->languageText('word_back', 'system');
echo $this->homeAndBackLink.' - '.$backLink->show();
?>
