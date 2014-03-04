<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixTwo();
?>
<div id="onecolumn">
    <div id="Canvas_Content_Body_Region2">
        {
        "display" : "block",
        "module" : "oer",
        "block" : "makeadaptation",

        <?php
        echo '"configData":';
        echo '"' . $productid . '|' . $mode . '|' . $id .'"';
        ?>
        }
        <div id="middledynamic_area" class="middledynamic_area_layer">&nbsp;</div>
    </div>
</div>

<?php
// Get the contents for the layout template 
$this->setVar('errors', Null);
//$this->setVar('mode', $mode);
//$this->setVar('title',$title);
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>