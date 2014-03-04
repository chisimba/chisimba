<?php
/**
*  A main content template for Species
*  Author: Derek Keats derek@localhost.local
*  Date: August 17, 2012, 2:22 pm
*
*/
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
$dog=1;
$cat=2;
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "species",
            "wrapStr" : 0,
            "block" : "alphalinked"
        }

        {
            "display" : "block",
            "module" : "species",
            "block" : "wikioverview"
        }
        
        {
            "display" : "block",
            "module" : "species",
            "block" : "flickrimgs"
        }

        <div id="leftdynamic_area" class="leftdynamic_area_layer"></div>
        <div id="leftfeedback_area" class="leftfeedback_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region3">
        {
            "display" : "block",
            "module" : "species",
            "block" : "groups"

        }
        {
            "display" : "block",
            "module" : "species",
            "block" : "changegroup"

        }        

        <div id="rightdynamic_area" class="rightdynamic_area_layer"></div>
        <div id="rightfeedback_area" class="rightfeedback_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "species",
            "wrapStr" : 0,
            "block" : "overview"

        }
        {
            "display" : "block",
            "module" : "species",
            "block" : "speciesmiddle"
        }
        {
            "display" : "block",
            "module" : "species",
            "block" : "userimages"
        }
        {
            "display" : "block",
            "module" : "species",
            "block" : "localsound"
        }
        {
            "display" : "block",
            "module" : "species",
            "block" : "eolimages"
        }
        {
            "display" : "block",
            "module" : "species",
            "block" : "speciessound"
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