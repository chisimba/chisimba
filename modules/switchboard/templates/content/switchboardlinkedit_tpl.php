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
<div id="onecolumn">
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "switchboard",
            "block" : "switchboardlinkedit"
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