<?php
/*
 * Created on Jan 28, 2007
 */
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Set columns to 2
$cssLayout->setNumColumns(2);

//Initialize NULL content for the left side column
$leftSideColumn = "";
//Get the menu creator
$objMenu = $this->getObject("leftmenu", "timeline");
//Add the left menu
$leftSideColumn = $objMenu->show();

//Add the templage heading to the main layer
$objH = $this->getObject('htmlheading', 'htmlelements');
//Heading H3 tag
$objH->type=3; 
$objH->str = $this->getParam("title", $objLanguage->languageText("mod_timeline_title_viewall", 'timeline'));
//Add the heading to the output string for the main display area
$rightSideColumn = $objH->show();

$rightSideColumn .= $str;

$showLinkFrame = $this->getParam('showLinkFrame', FALSE);
$thisScript = $_SERVER["REQUEST_URI"];
if ($showLinkFrame == "TRUE") {
    $showHide = $this->objLanguage->languageText("mod_timeline_hidelinked", "timeline");
    $thisScript = str_replace("showLinkFrame=TRUE", "showLinkFrame=FALSE", $thisScript); 
    $link = "<a href=\"" . $thisScript . "\">"
      . $showHide . "</a>";
} else {
    $showHide = $this->objLanguage->languageText("mod_timeline_showlinked", "timeline");
    if ($showLinkFrame=="FALSE") {
    	$thisScript = str_replace("showLinkFrame=FALSE", "showLinkFrame=TRUE", $thisScript); 
    } else {
    	$thisScript .= "&showLinkFrame=TRUE";
	}
    $link = "<a href=\"" . $thisScript . "\">"
      . $showHide . "</a>";
}
$rightSideColumn .= "<br />" . $link;

//Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
//Output the content to the page
echo $cssLayout->show();
?>  