<?php
/**
 *  A main content template for My notes
 *  Author: Nguni Phakela nguni52@gmail.com
 *  Date: March 16, 2012, 7:33 am
 *
 */
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div class='mynotes_left'>
        <div id="Canvas_Content_Body_Region1">
            {
            "display" : "block",
            "module" : "mynotes",
            "block" : "mynotesnav"
            }
            {
            "display" : "block",
            "module" : "mynotes",
            "block" : "mynotesleft"
            }

            <div id="leftdynamic_area" class="leftdynamic_area_layer"></div>
            <div id="leftfeedback_area" class="leftfeedback_area_layer"></div>
        </div>
    </div>
    <div class='mynotes_main'>
        <div id="Canvas_Content_Body_Region2">
            {
            "display" : "block",
            "module" : "mynotes",
            "block" : "mynotesmiddle"
            }
            <div id="middledynamic_area" class="middledynamic_area_layer">&nbsp;</div>
            <div id="middlefeedback_area" class="middlefeedback_area_layer">&nbsp;</div>
        </div></div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>