<?php
$name=$this->objUserBatch->exportName;
$size=strlen($this->objUserBatch->export);
$type='ASCII';

header("Content-type: $type");
header("Content-length: $size");
header("Content-Disposition: attachment; filename=$name");
header("Content-Description: PHP Generated Data");

    print $this->objUserBatch->export;

?>
