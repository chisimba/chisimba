<?php
#Load Inner classes
$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
$form = $this->objIEUtils->importTemplate($dbData, $this->getParam('packageType'), $newCourse);
echo $form.'<br/>';

?>