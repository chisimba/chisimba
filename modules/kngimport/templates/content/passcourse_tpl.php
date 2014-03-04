<?php
#Load Inner classes
$this->objIEUtils =  $this->newObject('importexportutils','kngimport');
$form = $this->objIEUtils->uploadTemplate('2');
echo $form.'<br/>';

?>
