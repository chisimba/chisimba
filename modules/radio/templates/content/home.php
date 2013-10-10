<?php

//block all skin banners
$this->setVar('pageSuppressToolbar',true);
$this->setVar('pageSuppressSkin',true);
$this->setVar('pageSuppressBanner',true);
$this->setVar('suppressFooter',true);

$line = "<center>$station Admin Panel</center>";

echo $line;

?>