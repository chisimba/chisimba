<?php
/****************************************************************
*  TEMPLATE TO DISPLAY AN LIST OF MODULES LOGGED                *
* **************************************************************/
//Grab a language instance
$objLanguage = & $this->getObject('language', 'language');

//Loop through and build up the new array
foreach ($ar as $line) {
    $modline = array();
    $modline['module'] = $line['module'];
    $modline['title'] = $objLanguage->languagetext('mod_' . $line['module'] . '_name');
    $modline['description'] = $objLanguage->languagetext('mod_' . $line['module'] . '_desc');
    $modarray[] = $modline;
} # foreach

//Kill off the array, we don't need it any more
unset($ar);

//Create an instance of the table object
$objTable = $this->newObject('htmltable', 'htmlelements');
//Turn on active rows
$objTable->active_rows=TRUE;
//Turn the array into a table
$objTable->arrayToTable($modarray);
//Show the table
echo $objTable->show();


//Create an instance of the link object
$objLink = $this->newObject('link', 'htmlelements');
//Set up the link URL for sortbydate
$location=$this->uri(array());
//Set the link for sortbydate
$objLink->href=$location;
//create the link text
$objLink->link=$objLanguage->languageText("mod_logger_backtomenu", "logger");
echo "<ul><li>".$objLink->show()."</li></ul><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>";
?>