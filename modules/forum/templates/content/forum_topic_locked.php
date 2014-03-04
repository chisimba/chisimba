<?php
//Sending display to 1 column layout
ob_start();

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>