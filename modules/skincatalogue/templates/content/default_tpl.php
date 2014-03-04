<?php
ob_start();
?>
<div id="column" >
    <div id="Canvas_Content_Body_Region2" >
        {
        "display" : "block",
        "module" : "skincatalogue",
        "block" : "center"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>