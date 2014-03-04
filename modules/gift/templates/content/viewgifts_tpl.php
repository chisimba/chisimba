<?php

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell("Name");
$table->addHeaderCell("Recipient");
$table->addHeaderCell("Value");
$table->addHeaderCell("Date");
$table->endHeaderRow();

foreach ($gifts as $gift) {
    $table->startRow();
    $table->addCell("giftname");
    $table->addCell("recipient");
    $table->addCell("value");
    $table->addCell("trandate");
    $table->endRow();
}

echo $table->show();
?>
