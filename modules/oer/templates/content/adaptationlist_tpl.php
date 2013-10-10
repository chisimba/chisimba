<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>
<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {
        "display" : "block",
        "module" : "language",
        "block" : "language"
        }
        {
        "display" : "block",
        "module" : "oer",
        "block" : "filterproduct",
        <?php
        echo '"configData":';
        echo '"' . $filteraction .'__'.$filteroptions. '"';
        ?>
        }

        <div id="leftdynamic_area" class="leftdynamic_area_layer"></div>
        <div id="leftfeedback_area" class="leftfeedback_area_layer"></div>
    </div>
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
        <div id="rightdynamic_area" class="rightdynamic_area_layer"></div>
        <div id="rightfeedback_area" class="rightfeedback_area_layer"></div>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
        "display" : "block",
        "module" : "oer",
        "block" : "adaptationslisting"
        <?php
        
        if (isset($filter)) {
        echo ',"configData":';
         if (isset($filter)) {
            echo '"' . $mode . '__' . $filter .'__'.$filteroptions. '"';
        } else {
            echo '"' . $mode . '"';
        }
        }
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