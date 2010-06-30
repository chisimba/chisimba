<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "test1"
        }
        {
            "display" : "block",
            "module" : "canvas",
            "block" : "selecttype"
        }
    </div>
    <div id="Canvas_Content_Body_Region3">
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "test2"
        }
        {
            "display" : "block",
            "module" : "security",
            "block" : "login"
        }
        {
            "display" : "block",
            "module" : "blocks",
            "block" : "wrapper"
        }
        {
            "display" : "block",
            "module" : "blocks",
            "block" : "table"
        }
        {
            "display" : "block",
            "module" : "filemanager",
            "block" : "userfiles"
        }
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "userdetails",
            "block" : "userdetails"
        }
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "nonexistentblock"
        }
        {
            "display" : "block",
            "module" : "dynamiccanvas",
            "block" : "thirdtest",
            "showToggle" : 0

        }
        {
            "display" : "block",
            "module" : "canvas",
            "block" : "canvasviewer"
        }
    </div>
</div>

<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>