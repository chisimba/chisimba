<?php
#Load Inner classes
$this->objIEUtils =  $this->newObject('importexportutils','contextadmin');
$form = $this->objIEUtils->uploadTemplate('2');
echo $form.'<br/>';

?>