<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlHeading();
$header->type = "1";
$header->cssClass = "useractivitytitle";
$header->str = ucfirst($this->objLanguage->code2Txt('mod_contextcontent_studentactivity', 'contextcontent', '[-readonly-] activity')) . '&nbsp;-&nbsp;' . $modulename.'&nbsp;('.$startdate.'&nbsp;-&nbsp;'.$enddate.')';

$homelink=new link($this->uri(array(
    "action"=>"showuseractivitybymodule",
    "startdate"=>$startdate,
    "enddate"=>$enddate,
    "moduleid"=>$modulename,
    )));
$homelink->link=$this->objLanguage->languageText("word_back", "system", "Back");


$exportLink = new link($this->uri(array("action" => "exportospreadsheet", "assignmentid" => $assignment['id'])));
$exportLink->link = $this->objLanguage->languageText('mod_assignment_export', 'assignment', 'Export to spreadsheet');
$exportStr = '&nbsp;&nbsp;|&nbsp;&nbsp' . $exportLink->show();

echo $header->show();
echo $this->objUser->fullname($userid);
//echo '<br/>'.$homelink->show();

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell('No');
$table->addHeaderCell('Date');
$table->addHeaderCell('Action');

$table->endHeaderRow();
$count = 1;
foreach ($data as $row) {
    $table->startRow();
    $table->addCell($count + ".");
    $table->addCell($row['createdon']);
    $table->addCell($row['action']);
    $table->endRow();
    $count++;
}
echo $table->show();
//echo $homelink->show();
?>
