<?php

/*
 *  Shows GUI for transfering users from use context to next
 */
$this->loadClass("radio", "htmlelements");
$this->loadClass("dropdown", "htmlelements");
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('button', 'htmlelements');


$header = new htmlheading();
$header->type = 3;
$header->str = ucwords($this->objLanguage->code2Txt('mod_context_importstudentsfromcontext', 'context', NULL, 'Import [-readonly-] from one [-context-] to another'));

echo $header->show();

if ($errormessages) {
    $header2 = new htmlheading();
    $header2->type = 3;
    $header2->str = $errormessages;

    echo $heade2->show();
}
$course1Dropdown = new dropdown('context1');
foreach ($data as $contextCode) {
    $row = $this->objContext->getContextDetails($contextCode);
    $course1Dropdown->addOption($row['contextcode'], $row['menutext']);
}
$course2Dropdown = new dropdown('context2');
foreach ($data as $contextCode) {
    $row = $this->objContext->getContextDetails($contextCode);
    $course2Dropdown->addOption($row['contextcode'], $row['menutext']);
}

$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->startRow();
$objTable->addCell(ucwords($this->objLanguage->code2Txt('mod_context_contextfrom', 'context', null, '[-context-] from')) . ":");
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($course1Dropdown->show());
$objTable->endRow();


$objTable->startRow();
$objTable->addCell(ucwords($this->objLanguage->code2Txt('mod_context_contextto', 'context', null, '[-context-] to')) . ":");
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($course2Dropdown->show());
$objTable->endRow();


$objButton = new button('save', $this->objLanguage->languageText('word_save'));
$objButton->extra = 'onclick="javascript:if(confirm(\'' . $this->objLanguage->code2Txt('mod_contextgroups_confirmtransfer', 'context', NULL, 'Are you sure you want to transfer these [-readonlys-]') . '?\')){document.confirmtransfercontextusers.submit();;}else{return false;}"';

$buttons = $objButton->show();

$objForm = new form('confirmtransfercontextusers', $this->uri(array('action' => 'savetransfercontextusers')));
$objForm->addToForm($objTable->show());
$objForm->addToForm($buttons);

echo $objForm->show();
?>
