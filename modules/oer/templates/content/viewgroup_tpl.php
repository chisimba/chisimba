<?php
/**
*  A main content template for OER group editor
*  Author: Paul Mungai paulwando@gmail.com
*  Date: February 08, 2012 21:42 am
*
*/
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>
<div id="twocolumn">
        <div id="Canvas_Content_Body_Region3">
        {
        "display" : "block",
        "module" : "oer",
        "block" : "featuredadaptation"
        }
         {
        "display" : "block",
        "module" : "oer",
        "block" : "browsebymap"
        
        }
        {
        "display" : "block",
        "module" : "calendar",
        "block" : "smallcalendar"
        }
        <div id="rightdynamic_area" class="rightdynamic_area_layer"></div>
        <div id="rightfeedback_area" class="rightfeedback_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "oer",
            "block" : "viewgroup",
        <?php
        echo '"configData":';
        echo '"' . $contextcode . '"';
        ?>
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