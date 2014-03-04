<?php

//Assignment list template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('link', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$objTableClass = $this->newObject('htmltable', 'htmlelements');
$objTableClass->startHeaderRow();
$objTableClass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordtitle', 'uwcelearningmobile') . '</strong>', '40%');
$objTableClass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date') . '</strong>', '40%');
$objTableClass->endHeaderRow();

$this->loadClass('htmlheading', 'htmlelements');
$objHead = new htmlheading();
$objHead->str = '&nbsp;' . $this->objLanguage->languageText('mod_assignment_assignments', 'assignment', 'Assignments');
$objHead->type = 4;
echo $objHead->show();

if (!empty($assignments)) {
    foreach ($assignments as $assignment) {
        if ($assignment['closing_date'] > date('Y-m-d H:i') || $this->objUser->isCourseAdmin($this->contextCode)) {
            $link = new link($this->URI(array('action' => 'viewassignment', 'id' => $assignment['id'])));
            $link->link = $assignment['name'];
            $str = $link->show();
        } else {
            $str = $assignment['name'];
        }
        $objTableClass->startRow();
        $objTableClass->addCell($str, '', 'center', 'left', $class);
        $objTableClass->addCell($this->objDate->formatDate($assignment['closing_date']), '', 'center', 'left', $class);
    }
} else {
    $norecords = $this->objLanguage->languageText('mod_uwcelearningmobile_wordnoass', 'uwcelearningmobile');
    $objTableClass->addCell($norecords, NULL, NULL, 'center', 'noRecordsMessage', 'colspan="7"');
}
$objFields = new fieldset();
$objFields->setLegend('');
$objFields->addContent($objTableClass->show());
echo $objFields->show();
echo $this->homeAndBackLink;
?>
