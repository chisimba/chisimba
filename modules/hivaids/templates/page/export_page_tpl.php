<?php
$size=strlen($display);
$type='ASCII';

header("Content-type: $type");
header("Content-length: $size");
header("Content-Disposition: attachment; filename=$name");
header("Content-Description: PHP Generated Data");

print $display;

?>