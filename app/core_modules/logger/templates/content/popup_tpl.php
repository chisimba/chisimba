<?php
/**
* Template to display the formatted log data
*/
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
                
echo $display;

?>