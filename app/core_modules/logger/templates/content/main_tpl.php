<?php
/****************************************************************
*  TEMPLATE TO DISPLAY AN ARRAY OF LOG DATA                     *
* **************************************************************/



//Create an instance of the table object
$objTable = $this->newObject('htmltable', 'htmlelements');
//Turn on active rows
$objTable->active_rows=TRUE;
//Turn the array into a table
$objTable->arrayToTable($ar);
//Show the table
echo $objTable->show();

//Grab a language instance
$objLanguage = & $this->getObject('language', 'language');
//Create an instance of the link object
$objLink = $this->newObject('link', 'htmlelements');
//Set up the link URL for sortbydate
$location=$this->uri(array());
//Set the link for sortbydate
$objLink->href=$location;
//create the link text
$objLink->link=$objLanguage->languageText("mod_logger_backtomenu");
echo "<ul><li>".$objLink->show()."</li></ul>";


?>