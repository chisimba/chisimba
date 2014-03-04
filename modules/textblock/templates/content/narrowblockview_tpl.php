<?php
/**
*  A main content template for link editor
*  Author: Derek Keats derek@dkeats.com
*  Date: December 18, 2011, 8:48 am
*
*/
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>
<div id="twocolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "textblock",
            "block" : "howto"
        }
        {
            "display" : "block",
            "module" : "textblock",
            "block" : "leftnav"
        }
        <div id="leftdynamic_area" class="leftdynamic_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "textblock",
            "block" : "shownarrowblocks"
        }
        <div id="middledynamic_area" class="middledynamic_area_layer">&nbsp;</div>
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>