<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlHeading();
$header->type = "1";
$header->cssClass = "useractivitytitle";
$header->str = $this->objLanguage->languageText('mod_contextcontent_useractivity', 'contextcontent', 'User activity') . '&nbsp;-&nbsp;' . $modulename.'&nbsp;('.$startdate.'&nbsp;-&nbsp;'.$enddate.')';
$homelink = new link($this->uri(array("action"=>"selectuseractivitybymoduledates")));
$homelink->link = $this->objLanguage->languageText("word_back", "system", "Back");


$exportLink = new link($this->uri(array("action" => "exportospreadsheet", "assignmentid" => $assignment['id'])));
$exportLink->link = $this->objLanguage->languageText('mod_assignment_export', 'assignment', 'Export to spreadsheet');
$exportStr = '&nbsp;&nbsp;|&nbsp;&nbsp' . $exportLink->show();

echo $header->show() . $homelink->show();

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell('No');
$table->addHeaderCell('Names');
$table->addHeaderCell('Access Count');
$table->addHeaderCell('Last Access');

$table->endHeaderRow();
$count = 1;
foreach ($data as $row) {
    $link = new link($this->uri(array(
        "action" => "viewuseractivitybyid",
        "userid" => $row['userid'],
        "startdate"=>$startdate,
        "enddate"=>$enddate,
        "moduleid"=>$modulename)));
    $link->link = $this->objUser->fullname($row['userid']);
    $table->startRow();
    $table->addCell($count + ".");
    $table->addCell($link->show());
    $table->addCell($row['accesscount']);
    $table->addCell($row['lastaccess']);
    $table->endRow();
    $count++;
}
$toolbar = $this->getObject('contextsidebar', 'context');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($toolbar->show());
$cssLayout->setMiddleColumnContent($table->show());

//echo $cssLayout->show();
echo $table->show();
echo $homelink->show();
?>
