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

$objTable = new htmltable();
$objTable->startRow();
$objTable->addCell('Address Books', '', '', '', 'heading', '');
$objTable->addCell('Entries', '20%', '', 'center', 'heading', '');
$objTable->addCell('', '10%', '', '', 'heading', '');
$objTable->endRow();
if ($arrBookList == false && empty($arrContextList)) {
    $objTable->startRow();
    $objTable->addCell($noBooksLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
    $objTable->endRow();
} else {
    if (!empty($arrContextList)) {
        foreach ($arrContextList as $context) {
            // get number of entries
            $groupId = $this->objGroupAdmin->getLeafId(array($context));
            $arrContextUserList = $this->objGroupAdmin->getGroupUsers($groupId, array('userId', 'firstName', 'surname', 'username'));
            $entries = count($arrContextUserList);

            $objLink = new link($this->uri(array('action' => 'addressbook', 'contextcode' => $context, 'currentFolderId' => $currentFolderId,)), 'internalmail');

            $objLink->link = $context;
            $contextLink = $objLink->show();

            $objTable->startRow();
            $objTable->addCell($contextLink, '', '', '', '', '');
            $objTable->addCell($entries, '', '', 'center', '', '');
            $objTable->addCell('', '', '', '', '', '');
            $objTable->endRow();
        }
    }
    $this->dbBookEntries = $this->newObject('dbbookentries', 'internalmail');
    if ($arrBookList != false) {
        foreach ($arrBookList as $book) {
            // get number of entries
            $arrBookEntriesList = $this->dbBookEntries->listBookEntries($book['id']);
            $entries = $arrBookEntriesList != false ? count($arrBookEntriesList) : 0;

            $objLink = new link($this->uri(array('action' => 'addressbook', 'bookId' => $book['id'], 'currentFolderId' => $currentFolderId,)), 'uwcelearningmobile');
            $objLink->link = $book['book_name'];
            if ($entries == 0 && $mode == 'show') {
                $bookName = $book['book_name'];
            } else {

                $bookName = $objLink->show();
            }

            $objTable->startRow();
            $objTable->addCell($bookName, '', '', '', '', '');
            $objTable->addCell($entries, '', '', 'center', '', '');
            $objTable->endRow();
        }
    }
}
echo 'Address books';
$objFields = new fieldset();
$objFields->setLegend('');
$objFields->addContent($objTable->show());
echo $objFields->show();

$backLink = new link($this->URI(array('action' => 'calladdrecipient')));
$backLink->link = $this->objLanguage->languageText('word_back', 'system');
echo $this->homeAndBackLink . ' - ' . $backLink->show();
?>
