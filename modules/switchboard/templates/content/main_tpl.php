<?php
/**
*  A main content template for Switchboard
*  Author: Derek Keats derek@dkeats.com
*  Date: January 4, 2012, 3:53 pm
*
*/
$objBuildCanvas = $this->getObject('buildcanvas', 'canvas');
echo $objBuildCanvas->show('module');
?>