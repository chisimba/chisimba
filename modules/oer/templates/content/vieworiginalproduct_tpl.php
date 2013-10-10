<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>
<div id="twocolumn">
   
    <div id="Canvas_Content_Body_Region3">
        {
        "display" : "block",
        "module" : "oer",
        "block" : "featuredoriginalproduct"
        }
         {
        "display" : "block",
        "module" : "oer",
        "block" : "mostarc"
        }
        <div id="rightdynamic_area" class="rightdynamic_area_layer"></div>
        <div id="rightfeedback_area" class="rightfeedback_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
        "display" : "block",
        "module" : "oer",
        "block" : "vieworiginalproduct",
        <?php
        echo '"configData":';
        echo '"' . $id . '"';
        ?>
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