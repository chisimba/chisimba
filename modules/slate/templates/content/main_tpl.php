<?php
/**
*  A main content template for A clean slate
*  Author: Derek Keats derek@dkeats.com
*  Date: January 25, 2012, 3:29 pm
*
*/
$objBuildCanvas = $this->getObject('buildcanvas', 'canvas');
echo $objBuildCanvas->show('page');
?>