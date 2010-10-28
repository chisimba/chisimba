<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlHeading();
$header->type = "1";
$header->cssClass = "useractivitytitle";
$header->str = $this->objLanguage->languageText('mod_contextcontent_toolsactivity', 'contextcontent', 'Tools activity') . '&nbsp;-&nbsp;' . $coursetitle . '&nbsp;(' . $startdate . '&nbsp;-&nbsp;' . $enddate . ')';

$homelink = new link($this->uri(array("action" => "selecttoolsadates")));
$homelink->link = $this->objLanguage->languageText("word_back", "system", "Back");
$homelink->link = $this->objLanguage->languageText("word_back", "system", "Back");

$exportLink = new link($this->uri(array("action" => "exportospreadsheet", "assignmentid" => $assignment['id'])));
$exportLink->link = $this->objLanguage->languageText('mod_assignment_export', 'assignment', 'Export to spreadsheet');
$exportStr = '&nbsp;&nbsp;|&nbsp;&nbsp' . $exportLink->show();

echo $header->show();

echo '<br/>' . $homelink->show();

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell('No');
$table->addHeaderCell('Tool');
$table->addHeaderCell('Activity count');

$table->endHeaderRow();
$count = 1;
foreach ($data as $row) {
    $link = new link($this->uri(array(
                        "action" => "showuseractivitybymodule",
                        "startdate" => $startdate,
                        "enddate" => $enddate,
                        "moduleid" => $row['module_id'],
                        "contextcode" => $contextcode
                    )));
    $link->link = $row['module_id'];
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
