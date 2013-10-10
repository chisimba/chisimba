<?php
/**
*  A page for adding or editing a note
*  Author: Nguni Phakela nguni52@gmail.com
*  Date: April 21, 2012, 6:33 am
*
*/
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
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
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "mynotes",
            "block" : "mynotesaddedit"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>