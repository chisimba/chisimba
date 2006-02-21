<?php
/****************************************************************
*  TEMPLATE TO DISPLAY THE LOGGER MENU                          *
* **************************************************************/
//Create an instance of the htmlHeading object
$objHeading = $this->newObject('htmlheading', 'htmlelements');
//Grab a language instance
$objLanguage = & $this->getObject('language', 'language');
//Add the heading text to the heading object, defaults to H3
$objHeading->str = $objLanguage->languageText("mod_logger_menutitle");
//Add the heading to the content string
echo $objHeading->show();

//Start an unordered list
echo "<ul>";
//Create an instance of the link object
$objLink = $this->newObject('link', 'htmlelements');



//Set up the link URL for sortbydate
$location=$this->uri(array('action'=>'showmoduleslogged'));
//Set the link for sortbydate
$objLink->href=$location;
//create the link text
$objLink->link=$objLanguage->languageText("mod_logger_showmoduleslogged");
echo "<li>".$objLink->show()."</li>";


//Set up the link URL for sortbydate
$location=$this->uri(array('action'=>'sortbydate'));
//Set the link for sortbydate
$objLink->href=$location;
//create the link text
$objLink->link=$objLanguage->languageText("mod_logger_sortbydate");
echo "<li>".$objLink->show()."</li>";

//Section for date selectors
echo "<ul>";

    //Set up the link URL for sortbydate
    $location=$this->uri(array('action'=>'sortbymodule', 'timeframe'=>'today'));
    //Set the link for sortbydate showing today only
    $objLink->href=$location;
    //create the link text
    $objLink->link=$objLanguage->languageText("mod_logger_showtoday");
    echo "<li>".$objLink->show()."</li>";
    
    //Set up the link URL for sortbydate
    $location=$this->uri(array('action'=>'sortbymodule', 'timeframe'=>'thisweek'));
    //Set the link for sortbydate showing today only
    $objLink->href=$location;
    //create the link text
    $objLink->link=$objLanguage->languageText("mod_logger_showthiswk");
    echo "<li>".$objLink->show()."</li>";
    
    //Set up the link URL for sortbydate
    $location=$this->uri(array('action'=>'sortbymodule', 'timeframe'=>'thismonth'));
    //Set the link for sortbydate showing today only
    $objLink->href=$location;
    //create the link text
    $objLink->link=$objLanguage->languageText("mod_logger_showthismo");
    echo "<li>".$objLink->show()."</li>";
    
echo "</ul>";

//Set up the link URL for sortbydate
$location=$this->uri(array('action'=>'sortbymodule'));
//Set the link for sortbydate
$objLink->href=$location;
//create the link text
$objLink->link=$objLanguage->languageText("mod_logger_sortbymodule");
echo "<li>".$objLink->show()."</li>";


//Set up the link URL for sortbydate
$location=$this->uri(array('action'=>'showstatsbyuser'));
//Set the link for sortbydate
$objLink->href=$location;
//create the link text
$objLink->link=$objLanguage->languageText("mod_logger_showstatsbyuser");
echo "<li>".$objLink->show()."</li>";

//Set up the link URL for sortbydate
$location=$this->uri(array('action'=>'showstatsbymodule'));
//Set the link for sortbydate
$objLink->href=$location;
//create the link text
$objLink->link=$objLanguage->languageText("mod_logger_showstatsbymodule");
echo "<li>".$objLink->show()."</li>";



//Close the unordered list
echo "</ul>";
?>
