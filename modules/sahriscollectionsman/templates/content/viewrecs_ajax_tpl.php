<?php
header ( "Content-Type: application/xhtml+xml;charset=utf-8" );
$this->objCollOps = $this->getObject('sahriscollectionsops');
//log_debug($this->objCollOps->formatRecord($msgs));
echo $this->objCollOps->formatRecord($msgs);
//echo "blergh";
exit ();
