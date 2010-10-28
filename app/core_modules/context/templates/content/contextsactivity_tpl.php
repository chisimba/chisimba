<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlHeading();
$header->type = "1";
$header->cssClass = "useractivitytitle";
$header->str = $this->objLanguage->languageText('mod_contextcontent_useractivity', 'contextcontent', 'User activity') . '&nbsp;-&nbsp;' . $modulename.'&nbsp;('.$startdate.'&nbsp;-&nbsp;'.$enddate.')';

$homelink = new link($this->uri(array("action"=>"selecttoolsadates")));$homelink->link=$this->objLanguage->languageText("word_back", "system", "Back");
$homelink->link = $this->objLanguage->languageText("word_back", "system", "Back");

$exportLink = new link($this->uri(array("action" => "exportospreadsheet", "assignmentid" => $assignment['id'])));
$exportLink->link = $this->objLanguage->languageText('mod_assignment_export', 'assignment', 'Export to spreadsheet');
$exportStr = '&nbsp;&nbsp;|&nbsp;&nbsp' . $exportLink->show();

echo $header->show();
echo '<br/>'.$homelink->show();

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell('No');
$table->addHeaderCell(ucfirst($this->objLanguage->code2Txt('word_context','system',NULL,'[-context-]')));
$table->addHeaderCell('Activity count');

$table->endHeaderRow();
$count = 1;
foreach ($data as $row) {
    $link=new link($this->uri(array(
    "action"=>"showtoolsactivity",
    "startdate"=>$startdate,
    "enddate"=>$enddate
    )));
    $link->link=$row['title'];
    $table->startRow();
    $table->addCell($count + ".");
    $table->addCell($link->show());
    $table->addCell($row['activitycount']);
    $table->endRow();
    $count++;
}
echo $table->show();
echo $homelink->show();
?>
