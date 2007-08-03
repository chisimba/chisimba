<?php
// Load Inner class.
$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
// Retrieve import template and display.
$form = $this->objIEUtils->importTemplate($dbData, $this->getParam('packageType'), $newCourse);
echo $form.'<br/>';

?>