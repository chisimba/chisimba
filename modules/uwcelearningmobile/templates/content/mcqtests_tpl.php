<?php

//MCQ Tests template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$objTable = $this->newObject('htmltable', 'htmlelements');
echo '<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordcourse', 'uwcelearningmobile') . ': </b>' . $this->contextTitle;

$objFields = new fieldset();
$objFields->setLegend('<b>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordtests', 'uwcelearningmobile') . '</b>');
$objTable->startHeaderRow();
$objTable->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordtitle', 'uwcelearningmobile') . '</strong>', '40%');
$objTable->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordduedate', 'uwcelearningmobile') . '</strong>', '40%');
$objTable->endHeaderRow();
if (!empty($tests)) {

    foreach ($tests as $test) {
        if ($test['status'] == 'open') {
            $objTable->startRow();
            $link = $test['name'];
            $objTable->addCell($link, '', 'center', 'left', $class);
            $objTable->addCell('' . $this->objDate->formatDate($test['closingdate']), '', 'center', 'left', $class);
            $objTable->endRow();
        }
    }
} else {
    $norec = $this->objLanguage->languageText('mod_uwcelearningmobile_wordnotest', 'uwcelearningmobile');
    $objTable->startRow();
    $objTable->addCell($norec, '', 'center', 'left', $class);
    $objTable->endRow();
}
$objFields->addContent($objTable->show() . '</br>');
echo '<br>' . $objFields->show() . '<br>' . $this->homeAndBackLink;
?>
