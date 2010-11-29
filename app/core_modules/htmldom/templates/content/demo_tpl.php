<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "security",
            "block" : "login"
        }
    </div>
    <div id="Canvas_Content_Body_Region3">

    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "htmldom",
            "block" : "hello"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>
