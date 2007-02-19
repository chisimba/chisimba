<?php
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Set columns to 2
$cssLayout->setNumColumns(2);
//Initialize NULL content for the left side column
$leftSideColumn = "";


//Add the templage heading to the main layer
$objH = $this->getObject('htmlheading', 'htmlelements');
//Heading H3 tag
$objH->type=3; 
$objH->str = $objLanguage->languageText("mod_timeline_title_viewall", 'timeline');
//Add the heading to the output string for the main display area
$rightSideColumn = $objH->show();

$rightSideColumn .= $str;

//Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
//Output the content to the page
echo $cssLayout->show();
?>
