<?php
/* -------------------- template for messaging module ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package sitepermissions
*/

/**
* Template for the sitepermissions module
* Author Kevin Cyster
* */
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('layer', 'htmlelements');

$heading = $this->objLanguage->code2Txt('mod_sitepermissions_name', 'sitepermissions');

// headings
$objHeader = new htmlheading();
$objHeader->str = ucfirst($heading);
$objHeader->type = 1;
$header = $objHeader->show();        

$objLayer = new layer();
$objLayer->addToStr($header);
$objLayer->padding = '10px';
$layer = $objLayer->show();
echo $layer;
        
echo $templateContent;
?>