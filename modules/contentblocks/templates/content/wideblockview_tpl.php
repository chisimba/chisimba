<?php
/**
*  A main content template for link editor
*  @author    Paul Mungai paulwando@gmail.com
*  @copyright 2012 AVOIR
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
            "module" : "contentblocks",
            "block" : "contentblockshowto"
        }
        {
            "display" : "block",
            "module" : "contentblocks",
            "block" : "contentleftnav"
        }
        <div id="leftdynamic_area" class="leftdynamic_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "contentblocks",
            "block" : "contentwideblocks"
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