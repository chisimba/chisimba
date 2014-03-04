<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlHeading();
$header->type = "1";
$header->cssClass = "useractivitytitle";
$header->str = ucfirst('User activity &nbsp;('.$startdate.'&nbsp;-&nbsp;'.$enddate.')');

$homelink=new link($this->uri(array()));
$homelink->link=$this->objLanguage->languageText("word_back", "system", "Back");



echo $header->show();

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell('No');
$table->addHeaderCell('User');
$table->addHeaderCell('Action');
$table->addHeaderCell('Date');

$table->endHeaderRow();
$count = 1;
foreach ($data as $row) {
    $table->startRow();
    $table->addCell($count + ".");
    $table->addCell($this->objUser->fullname($row['userid']));
    $table->addCell($row['action']);
    $table->addCell($row['createdon']);
    $table->endRow();
    $count++;
}
echo $table->show();
//echo $homelink->show();
?>
