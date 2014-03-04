<?php
$this->objInput =  & $this->getObject("commentinterface");
$tableName = $this->getParam('tableName', NULL);
$sourceId = $this->getParam('sourceId', NULL);
$moduleCode = $this->getParam('moduleCode', NULL);

echo $this->objInput->renderInput($tableName, $sourceId, $moduleCode);
?>
