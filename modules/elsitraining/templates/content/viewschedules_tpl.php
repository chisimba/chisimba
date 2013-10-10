<?php

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell("Description");
$table->addHeaderCell("Start Time");
$table->addHeaderCell("End Time");
$table->addHeaderCell("Venue");
$table->addHeaderCell("Contact Person");
$table->addHeaderCell("Limit");
$table->endHeaderRow();

foreach ($schedules as $schedule) {
    $table->startRow();
    $table->addCell($schedule['description']);
    $table->addCell($schedule['starttime']);
    $table->addCell($schedule['endtime']);
    $table->addCell($schedule['venue']);
    $table->addCell($schedule['contactperson']);
    $table->addCell($schedule['maxlimit']);
    $table->endRow();
    $viewDetailsLink = new link($this->uri(array('action' => 'view', 'id' => $schedule['id'])));
    $viewDetailsLink->link = 'Groover';
    $viewDetailsLink->show();

}

echo $table->show();
?>
