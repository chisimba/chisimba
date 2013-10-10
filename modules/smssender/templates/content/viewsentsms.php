<?php

$objIcon =& $this->newObject('geticon', 'htmlelements');

echo '<h1>Details of Sent SMS</h1><br />';

$table = $this->getObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('Number:', 100);
$table->addCell($sms['recipientnumber']);
$table->endRow();

$table->startRow();
$table->addCell('Message:');
$table->addCell($sms['message']);
$table->endRow();

$table->startRow();
$table->addCell('Date:');
$table->addCell($sms['datesent']);
$table->endRow();

$table->startRow();
$table->addCell('Result:');

if ($sms['result'] == 'Y') {
    $smsResult = 'SMS Sent Successfully - '.$sms['messageid'];
} else {
    $smsResult = 'SMS Could not be sent - '.$sms['messageid'];
}
$table->addCell($smsResult);
$table->endRow();

echo $table->show();
?>