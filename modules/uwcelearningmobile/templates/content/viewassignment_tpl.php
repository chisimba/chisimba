<?php

//Assignment spec template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('link', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$objFields = new fieldset();
$objWashout = $this->getObject('washout', 'utilities');
$objTrimStr = $this->getObject('trimstr', 'strings');
$objFields->setLegend('<b>' . $assignment['name'] . '</b>');

if ($this->objUser->isCourseAdmin($this->contextCode)) {

    $objFields = new fieldset();
    $objFields->setLegend('<b>' . $assignment['name'] . '</b>');

    $table = new htmltable();
    $this->objUtil = $this->getObject('util');
    $submissions = $this->objUtil->getStudentSubmissions($assignment['id']);
    $table->startHeaderRow();
    $table->addHeaderCell(ucwords($this->objLanguage->code2Txt('mod_assignment_studname', 'assignment', NULL, '[-readonly-] Name')));
    $table->addHeaderCell($this->objLanguage->languageText('mod_assignment_datesubmitted', 'assignment', 'Date Submitted'));
    $table->endHeaderRow();

    if (count($submissions) == 0) {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_assignment_noassignmentssubmitted', 'assignment', 'No Assignments Submitted Yet'), NULL, NULL, NULL, 'noRecordsMessage', ' colspan="4"');
        $table->endRow();
    } else {

        foreach ($submissions as $submission) {
            $table->startRow();

            $link = new link($this->uri(array('action' => 'viewsubmission', 'id' => $submission['id'])));
            $link->link = $this->objUser->fullName($submission['userid']);

            $table->addCell($link->show());
            $table->addCell($this->objDate->formatDate($submission['datesubmitted']));
            $table->endRow();
        }
    }
    $objFields->addContent($table->show());
    echo $objFields->show();
}



$table = new htmltable();
$objFields = new fieldset();
$objFields->setLegend('<b>' . $assignment['name'] . ' - Info</b>');

$table->startRow();
$table->addCell('<strong>' . ucwords($this->objLanguage->code2Txt('mod_assignment_lecturer', 'assignment', NULL, '[-author-]')) . ':</strong>', 130);
$table->addCell($this->objUser->fullName($assignment['userid']));
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_openingdate', 'assignment', 'Opening Date') . '</strong>', 130);
$table->addCell($this->objDate->formatDate($assignment['opening_date']));
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date') . '</strong>', 130);
$table->addCell($this->objDate->formatDate($assignment['closing_date']));
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark') . '</strong>', 130);
$table->addCell($assignment['mark']);
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_percentyrmark', 'assignment', 'Percentage of year mark') . ':</strong>', 200, NULL, NULL, 'nowrap');
$table->addCell($assignment['percentage'] . '%');
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type') . '</strong>', 130);
if ($assignment['format'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_assignment_online', 'assignment', 'Online'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_assignment_upload', 'assignment', 'Upload'));
}
$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('word_description', 'system', 'Description') . ': </strong>', 130);
$table->endRow();
$table->startRow();
$table->addCell($objWashout->parseText($assignment['description']), NULL, NULL, NULL, NULL, ' colspan="3"');
$table->endRow();
$table->endRow();
$objFields->addContent($table->show());
echo $objFields->show();

$backLink = new link($this->URI(array('action' => 'assignment')));
$backLink->link = 'Back to Assignments';
echo '<br>' . $this->homeAndBackLink . ' - ' . $backLink->show();
?>
