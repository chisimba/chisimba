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


//Add the templage heading to the main layer
$objH = $this->getObject('htmlheading', 'htmlelements');
//Heading H3 tag
$objH->type=3; 
$objH->str = $this->getParam("title", $objLanguage->languageText("mod_timeline_title_viewall", 'timeline'));
//Add the heading to the output string for the main display area
$rightSideColumn = $objH->show();

$rightSideColumn .= $str;

$showLinkFrame = $this->getParam('showLinkFrame', FALSE);
if ($showLinkFrame == "TRUE") {
    $showHide = "Hide region to display linked content";
    $link = "<a href=\"" . $this->uri(array(), "timeline") . "\">"
      . $showHide . "</a>";
} else {
    $showHide = "Show region to display linked content";
    $link = "<a href=\"" . $this->uri(array('showLinkFrame' => 'TRUE'), "timeline") . "\">"
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