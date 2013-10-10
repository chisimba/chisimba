<?php
/**
*  A main content template for Simple feedback
*  Author: Derek Keats derekkeats@gmail.com
*  Date: September 17, 2012, 8:14 pm
*
*/
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
Your assistance in helping us to improve Software Freedom Day 2013 is much appreciated.
        <div id="leftdynamic_area" class="leftdynamic_area_layer"></div>
        <div id="leftfeedback_area" class="leftfeedback_area_layer"></div>
    </div>

    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "simplefeedback",
            "block" : "sffeedback"
        }
        <div id="middledynamic_area" class="middledynamic_area_layer">&nbsp;</div>
        <div id="middlefeedback_area" class="middlefeedback_area_layer">&nbsp;</div>
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>