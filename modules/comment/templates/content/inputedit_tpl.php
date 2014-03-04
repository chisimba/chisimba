<?php
$this->objInput =  & $this->getObject("commentinterface");
//print_r($_GET);
//die("tester");
$tableName = $this->getParam('tableName', NULL);

$sourceId =$this->getParam('sourceid', NULL);
$moduleCode = $this->getParam('moduleCode', NULL);
$id = $this->getParam('id', NULL);

echo $this->objInput->renderInputEdit($tableName, $sourceId, $moduleCode, $id);
?>
